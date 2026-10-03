from fastapi import FastAPI, UploadFile, File, HTTPException
import cv2
import numpy as np
import os
from ultralytics import YOLO
from collections import Counter
from sklearn.cluster import KMeans

app = FastAPI(
    title="Petopia AI Advanced Service",
    description="FastAPI Microservice for Breed & Color Analysis"
)


MODEL_PATH = "/app/scripts/runs/classify/train9/weights/best.pt"
model = YOLO(MODEL_PATH if os.path.exists(MODEL_PATH) else "yolov8n-cls.pt")
class_names = model.names

CAT_BREEDS = [
    "Abyssinian", "American Shorthair", "Bengal", "Birman", "Bombay",
    "British Shorthair", "Domestic Shorthair", "Egyptian Mau", "Himalayan",
    "Maine Coon", "Persian", "Ragdoll", "Russian Blue", "Siamese", "Sphynx"
]
CONF_THRESHOLD = 0.60

def rgb_to_color_name(r, g, b):
    colors = {
        'Black': [(0, 0, 0), (50, 50, 50)], 'White': [(200, 200, 200), (255, 255, 255)],
        'Brown': [(101, 67, 33), (160, 120, 80)], 'Light Brown': [(160, 120, 80), (200, 160, 120)],
        'Golden': [(255, 215, 0), (255, 255, 150)], 'Grey': [(50, 50, 50), (200, 200, 200)],
        'Orange': [(255, 140, 0), (255, 200, 100)], 'Red': [(150, 0, 0), (255, 100, 100)],
        'Yellow': [(200, 200, 0), (255, 255, 200)], 'Cream': [(245, 245, 220), (255, 255, 240)],
        'Tan': [(210, 180, 140), (240, 220, 180)], 'Silver': [(192, 192, 192), (220, 220, 220)],
    }
    min_distance = float('inf')
    closest_color = 'Mixed'
    for color_name, (min_rgb, max_rgb) in colors.items():
        if (min_rgb[0] <= r <= max_rgb[0] and min_rgb[1] <= g <= max_rgb[1] and min_rgb[2] <= b <= max_rgb[2]):
            return color_name
        center_r, center_g, center_b = np.mean([min_rgb, max_rgb], axis=0)
        distance = ((r - center_r) ** 2 + (g - center_g) ** 2 + (b - center_b) ** 2) ** 0.5
        if distance < min_distance:
            min_distance = distance
            closest_color = color_name
    return closest_color

def analyze_single_image_colors(image):
    img_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    h, w = img_rgb.shape[:2]
    if w > 300:
        img_rgb = cv2.resize(img_rgb, (300, int(h * (300 / w))))
    pixels = img_rgb.reshape((-1, 3))
    mask = np.logical_and(np.sum(pixels, axis=1) > 30, np.sum(pixels, axis=1) < 700)
    filtered_pixels = pixels[mask] if np.sum(mask) > 10 else pixels
    
    n_clusters = min(5, len(filtered_pixels))
    kmeans = KMeans(n_clusters=n_clusters, random_state=42, n_init=10)
    kmeans.fit(filtered_pixels)
    colors, labels = kmeans.cluster_centers_, kmeans.labels_
    label_counts = Counter(labels)
    
    color_frequencies = sorted([(colors[i], label_counts[i] / len(labels)) for i in range(n_clusters)], key=lambda x: x[1], reverse=True)
    color_names = []
    for color, frequency in color_frequencies:
        if frequency > 0.05:
            c_name = rgb_to_color_name(int(color[0]), int(color[1]), int(color[2]))
            if c_name and c_name not in color_names:
                color_names.append(c_name)
    return color_names[:3]

@app.post("/api/v1/predict-breed")
async def predict_breed_api(files: list[UploadFile] = File(...)):
    try:
        predictions = []
        individual_results = []

        for file in files:
            contents = await file.read()
            nparr = np.frombuffer(contents, np.uint8)
            image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
            
            if image is None:
                continue

            results = model.predict(source=image, imgsz=224, verbose=False)
            probs = results[0].probs
            top_idx = int(probs.top1)
            top_conf = float(probs.top1conf)
            breed_name = class_names[top_idx]

            pred = "Mixed Breed" if top_conf < CONF_THRESHOLD else breed_name
            predictions.append(pred)
            species = "Cat" if breed_name in CAT_BREEDS else "Dog"

            individual_results.append({
                "image_path": file.filename,
                "prediction": pred.replace("_", " "),
                "confidence": top_conf,
                "species": species
            })

        if not individual_results:
            return {"success": False, "error": "No valid images processed"}

        if len(individual_results) == 1:
            res = individual_results[0]
            return {
                "success": True,
                "prediction": res["prediction"],
                "confidence": res["confidence"],
                "species": res["species"],
                "individual_predictions": individual_results
            }


        count = Counter(predictions)
        if "Mixed Breed" in count:
            del count["Mixed Breed"]

        if len(count) == 0:
            final_answer, final_species = "Mixed Breed", "Dog"
        else:
            breed, freq = count.most_common(1)[0]
            if freq == 1:
                final_answer, final_species = "Mixed Breed", "Dog"
            else:
                final_answer = breed
                final_species = "Cat" if breed in CAT_BREEDS else "Dog"

        return {
            "success": True,
            "final_prediction": final_answer.replace("_", " "),
            "final_species": final_species,
            "individual_predictions": individual_results,
            "vote_summary": dict(Counter(predictions))
        }
    except Exception as e:
        return {"success": False, "error": str(e)}


@app.post("/api/v1/predict-colors")
async def predict_colors_api(files: list[UploadFile] = File(...)):
    try:
        all_colors = []
        individual_results = []

        for file in files:
            contents = await file.read()
            nparr = np.frombuffer(contents, np.uint8)
            image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
            
            if image is None:
                continue

            colors = analyze_single_image_colors(image)
            individual_results.append({
                'image_path': file.filename,
                'colors': colors
            })
            all_colors.extend(colors)

        if not all_colors:
            return {"success": True, "colors": ["Mixed"], "individual_results": [], "color_frequency": {"Mixed": 1}}

        color_counter = Counter(all_colors)
        most_common_colors = [color for color, count in color_counter.most_common(3)]

        return {
            'success': True,
            'colors': most_common_colors,
            'individual_results': individual_results,
            'color_frequency': dict(color_counter)
        }
    except Exception as e:
        return {"success": False, "error": str(e)}