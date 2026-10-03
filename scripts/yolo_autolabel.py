import os
import shutil
import random
import yaml

# -----------------------------
# CONFIGURATION
# -----------------------------
dataset_path = "/home/pohling/Pet-Adoption-and-Care-system/yolo_datasett"  # Original dataset
output_path = "/home/pohling/Pet-Adoption-and-Care-system/ready_dataset"    # YOLO-ready output
split_ratio = {"train": 0.8, "val": 0.1, "test": 0.1}

CLASSIFICATION_MODE = True  # True for classification (full-image), False for detection

# -----------------------------
# HELPER FUNCTIONS
# -----------------------------
def create_folders(base_path, splits, class_names):
    """Create empty folders for all splits and classes"""
    for split in splits:
        split_path = os.path.join(base_path, split)
        os.makedirs(split_path, exist_ok=True)
        for class_name in class_names:
            class_folder = os.path.join(split_path, class_name)
            os.makedirs(class_folder, exist_ok=True)

def split_images(image_list, ratio):
    random.shuffle(image_list)
    total = len(image_list)
    train_end = int(total * ratio["train"])
    val_end = train_end + int(total * ratio["val"])
    return image_list[:train_end], image_list[train_end:val_end], image_list[val_end:]

def copy_images(file_list, src_folder, dest_folder):
    """Copy images into destination folder"""
    for f in file_list:
        shutil.copy(os.path.join(src_folder, f), os.path.join(dest_folder, f))

# -----------------------------
# DATASET PROCESSING
# -----------------------------
def process_dataset(dataset_type):
    type_path = os.path.join(dataset_path, dataset_type)
    class_names = sorted([d for d in os.listdir(type_path) if os.path.isdir(os.path.join(type_path, d))])
    print(f"\nProcessing '{dataset_type}' with classes: {class_names}")

    # Create YOLO folder structure
    yolo_type_path = os.path.join(output_path, dataset_type)
    create_folders(yolo_type_path, split_ratio.keys(), class_names)

    # Copy images to splits
    for class_name in class_names:
        class_folder = os.path.join(type_path, class_name)
        images = [f for f in os.listdir(class_folder) if f.lower().endswith((".jpg", ".jpeg", ".png"))]
        train_imgs, val_imgs, test_imgs = split_images(images, split_ratio)

        split_map = {"train": train_imgs, "val": val_imgs, "test": test_imgs}
        for split_name, img_list in split_map.items():
            dest_folder = os.path.join(yolo_type_path, split_name, class_name)
            copy_images(img_list, class_folder, dest_folder)

    # Create data.yaml
    data_yaml = {
        "train": os.path.join(yolo_type_path, "train"),
        "val": os.path.join(yolo_type_path, "val"),
        "test": os.path.join(yolo_type_path, "test"),
        "nc": len(class_names),
        "names": {i: name for i, name in enumerate(class_names)}
    }
    yaml_file = os.path.join(yolo_type_path, "data.yaml")
    with open(yaml_file, "w") as f:
        yaml.dump(data_yaml, f, sort_keys=False)
    print(f"✅ Saved data.yaml at {yaml_file}")

# -----------------------------
# MAIN PROCESS
# -----------------------------
print("=" * 60)
print("YOLO Classification Dataset Preparation Script")
print("=" * 60)

datasets = ["breed", "species"]
for dataset_type in datasets:
    process_dataset(dataset_type)

print("\n🎉 All datasets processed and YOLO-ready!")
print("Next steps:")
print("1. Check that all splits contain the same class folders")
print("2. Train your model using:")
print("   yolo classify train data=/home/user/yolo_dataset/<dataset>/data.yaml model=yolov8n-cls.pt epochs=70")
