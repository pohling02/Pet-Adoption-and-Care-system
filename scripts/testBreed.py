from ultralytics import YOLO
from collections import Counter

# Load model
model_path = "/home/user/Pet-Adoption-and-Care-system/scripts/runs/classify/train9/weights/best.pt"
model = YOLO(model_path)

# Images to test
image_paths = [
    "/home/user/dataset/husk.jpg",
    "/home/user/dataset/mocha.png",
    "/home/user/dataset/husky.jpg"
]

# Predict
results = model.predict(source=image_paths, imgsz=224)
class_names = model.names
CONF_THRESHOLD = 0.50

predictions = []

# Individual image predictions
for res in results:
    probs = res.probs
    top_idx = probs.top1
    top_conf = float(probs.top1conf)

    if top_conf < CONF_THRESHOLD:
        predictions.append("Mix Breed")
    else:
        predictions.append(class_names[top_idx])

# ===== Majority Vote Final Decision =====
# Count predictions
count = Counter(predictions)

# Remove "Mix Breed" from voting
if "Mix Breed" in count:
    del count["Mix Breed"]

if len(count) == 0:
    final_answer = "Mixed Breed"
else:
    # Get the most common breed and its count
    breed, freq = count.most_common(1)[0]

    # If majority only appears once → unsure → mix breed
    if freq == 1:
        final_answer = "Mixed Breed"
    else:
        final_answer = breed

print("===== FINAL ANSWER =====")
print("Individual predictions:", predictions)
print("Final Conclusion:", final_answer)
