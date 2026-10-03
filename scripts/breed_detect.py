import sys
import json
from ultralytics import YOLO
from collections import Counter

model_path = "/home/pohling/Pet-Adoption-and-Care-system/scripts/runs/classify/train9/weights/best.pt"
model = YOLO(model_path)

# Use model's internal class list (correct order)
class_names = model.names

# Cat breeds (cleaned to match folder naming)
CAT_BREEDS = [
    "Abyssinian", "American Shorthair", "Bengal", "Birman", "Bombay",
    "British Shorthair", "Domestic Shorthair", "Egyptian Mau", "Himalayan",
    "Maine Coon", "Persian", "Ragdoll", "Russian Blue", "Siamese", "Sphynx"
]

CONF_THRESHOLD = 0.60

def predict_breed(image_paths):
    """Predict breed for single or multiple images, processing one by one"""
    try:
        # Convert single path to list
        if isinstance(image_paths, str):
            image_paths = [image_paths]
        
        predictions = []
        individual_results = []

        # Process each image ONE BY ONE
        for image_path in image_paths:
            results = model.predict(source=image_path, imgsz=224, verbose=False)
            probs = results[0].probs
            top_idx = int(probs.top1)
            top_conf = float(probs.top1conf)
            breed_name = class_names[top_idx]

            # Determine prediction
            if top_conf < CONF_THRESHOLD:
                pred = "Mixed Breed"
            else:
                pred = breed_name

            predictions.append(pred)
            
            # Store individual result
            species = "Cat" if breed_name in CAT_BREEDS else "Dog"
            individual_results.append({
                "image_path": image_path,
                "prediction": pred.replace("_", " "),
                "confidence": top_conf,
                "species": species
            })

        # Single image - return consistent format with success field
        if len(image_paths) == 1:
            result = individual_results[0]
            return {
                "success": True,
                "prediction": result["prediction"],
                "confidence": result["confidence"],
                "species": result["species"],
                "individual_predictions": individual_results  # Include for consistency
            }

        # ===== Majority Vote Final Decision (Multiple Images) =====
        count = Counter(predictions)

        # Remove "Mixed Breed" from voting
        if "Mixed Breed" in count:
            del count["Mixed Breed"]

        if len(count) == 0:
            final_answer = "Mixed Breed"
            final_species = "Dog"
        else:
            # Get the most common breed and its count
            breed, freq = count.most_common(1)[0]

            # If majority only appears once → unsure → mixed breed
            if freq == 1:
                final_answer = "Mixed Breed"
                final_species = "Dog"
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
        return {
            "success": False,
            "error": str(e)
        }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "No image path provided"}))
        sys.exit(1)

    # Get all image paths from command line arguments
    image_paths = sys.argv[1:]
    
    result = predict_breed(image_paths)
    print(json.dumps(result))