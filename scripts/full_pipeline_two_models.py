#!/usr/bin/env python3
import os
import shutil
import random
import yaml
import subprocess
import time
from pathlib import Path
from glob import glob

# -----------------------------
# CONFIG
# -----------------------------
dataset_path = "/home/user/dataset_backup"     
output_path = "/home/user/yolo_dataset"         
split_ratio = {"train": 0.8, "val": 0.1, "test": 0.1}
yolo_model_pretrained = "yolov8n.pt"            
autolabel_epochs = 3                            
final_epochs = 0                               
imgsz = 640
random_seed = 42
datasets = ["breed", "species"]
project_runs_dir = "runs"                       

random.seed(random_seed)

# -----------------------------
# helpers
# -----------------------------
def create_folders(base_path, splits):
    for split in splits:
        os.makedirs(os.path.join(base_path, split, "images"), exist_ok=True)
        os.makedirs(os.path.join(base_path, split, "labels"), exist_ok=True)

def split_images(image_list, ratio):
    random.shuffle(image_list)
    total = len(image_list)
    train_end = int(total * ratio["train"])
    val_end = train_end + int(total * ratio["val"])
    return image_list[:train_end], image_list[train_end:val_end], image_list[val_end:]

def copy_images(file_list, src_folder, dest_folder):
    for f in file_list:
        src = os.path.join(src_folder, f)
        dst = os.path.join(dest_folder, f)
        if not os.path.exists(dst):
            shutil.copy2(src, dst)

def save_data_yaml(yolo_type_path, class_names):
    data_yaml = {
        "train": os.path.join(yolo_type_path, "train", "images"),
        "val": os.path.join(yolo_type_path, "val", "images"),
        "test": os.path.join(yolo_type_path, "test", "images"),
        "nc": len(class_names),
        "names": {i: name for i, name in enumerate(class_names)}
    }
    yaml_file = os.path.join(yolo_type_path, "data.yaml")
    with open(yaml_file, "w") as f:
        yaml.dump(data_yaml, f, sort_keys=False)
    return yaml_file

def find_latest_labels_dir(base_runs=project_runs_dir):
    # search for any 'labels' dirs created by ultralytics inside run folders
    cand = glob(os.path.join(base_runs, "**", "labels"), recursive=True)
    if not cand:
        return None
    # choose the most recently modified labels folder
    latest = max(cand, key=lambda p: os.path.getmtime(p))
    return latest

def move_autolabels_to_dataset(labels_dir, yolo_type_path):
    """
    labels_dir: folder with .txt label files created by ultralytics
    yolo_type_path: /home/user/yolo_dataset/<dataset_type>
    We will map each label to whichever split (train/val/test) contains the image file.
    """
    if not labels_dir or not os.path.exists(labels_dir):
        print("No labels_dir found to move.")
        return 0

    moved = 0
    for label_path in Path(labels_dir).glob("*.txt"):
        image_name = label_path.stem  # filename without extension
        # try to detect which split contains the corresponding image
        found = False
        for split in ["train", "val", "test"]:
            img_folder = Path(yolo_type_path) / split / "images"
            # check for multiple possible extensions
            for ext in (".jpg", ".jpeg", ".png", ".bmp"):
                if (img_folder / (image_name + ext)).exists():
                    dest_label_dir = Path(yolo_type_path) / split / "labels"
                    dest_label_dir.mkdir(parents=True, exist_ok=True)
                    shutil.copy2(str(label_path), str(dest_label_dir / (image_name + ".txt")))
                    moved += 1
                    found = True
                    break
            if found:

                
                break
        if not found:
            # if not found in splits, place into train/labels by default
            dest = Path(yolo_type_path) / "train" / "labels"
            dest.mkdir(parents=True, exist_ok=True)
            shutil.copy2(str(label_path), str(dest / (image_name + ".txt")))
            moved += 1
    return moved

def run_ultralytics_train(data_yaml, model, epochs, imgsz, project=None, name=None, extra_args=None):
    """
    Call ultralytics train via Python module to avoid 'yolo' CLI missing issues.
    Returns subprocess.CompletedProcess
    """
    cmd = ["python3", "-m", "ultralytics", "train", f"data={data_yaml}", f"model={model}", f"epochs={epochs}", f"imgsz={imgsz}"]
    if project:
        cmd.append(f"project={project}")
    if name:
        cmd.append(f"name={name}")
    if extra_args:
        cmd += extra_args
    print("Running:", " ".join(cmd))
    res = subprocess.run(cmd, check=False)
    return res

# -----------------------------
# main pipeline per dataset
# -----------------------------
def process_dataset(dataset_type):
    print(f"\n--- Processing {dataset_type} ---")
    type_path = os.path.join(dataset_path, dataset_type)
    if not os.path.isdir(type_path):
        raise FileNotFoundError(f"{type_path} not found")

    # discover classes (subfolders)
    class_names = sorted([d for d in os.listdir(type_path) if os.path.isdir(os.path.join(type_path, d))])
    print(f"Found classes: {class_names}")

    # create YOLO dataset structure
    yolo_type_path = os.path.join(output_path, dataset_type.lower())  # use lowercase folder like 'breed'
    create_folders(yolo_type_path, split_ratio.keys())

    # split and copy images
    for class_name in class_names:
        class_folder = os.path.join(type_path, class_name)
        images = [f for f in os.listdir(class_folder) if f.lower().endswith((".jpg", ".jpeg", ".png", ".bmp"))]
        train_imgs, val_imgs, test_imgs = split_images(images, split_ratio)
        copy_images(train_imgs, class_folder, os.path.join(yolo_type_path, "train", "images"))
        copy_images(val_imgs, class_folder, os.path.join(yolo_type_path, "val", "images"))
        copy_images(test_imgs, class_folder, os.path.join(yolo_type_path, "test", "images"))

    # save data.yaml
    yaml_file = save_data_yaml(yolo_type_path, class_names)
    print(f"Saved data.yaml -> {yaml_file}")

    # -----------------------------
    # Step A: Auto-labeling (minimal training run with --auto)
    # We'll run a short training with --auto to generate labels.
    # Use project and name so the run folder is predictable.
    # -----------------------------
    autolabel_project = "autolabel_runs"
    autolabel_name = f"autolabel_{dataset_type.lower()}_{int(time.time())}"
    # Run ultralytics train with --auto; pass extra arg ["--auto"]
    # Use minimal epochs to speed up label generation
    res = run_ultralytics_train(yaml_file, yolo_model_pretrained, autolabel_epochs, imgsz,
                                project=autolabel_project, name=autolabel_name, extra_args=["--auto"])
    if res.returncode != 0:
        print(f"Autolabel (ultralytics) returned non-zero exit code {res.returncode}. Check output above.")
        # continue to attempt to find labels anyway

    # find the labels folder created by ultralytics
    # ultralytics creates a 'labels' dir inside the run folder; locate the newest labels dir under autolabel_project
    labels_dir = find_latest_labels_dir(base_runs=autolabel_project)
    if not labels_dir:
        # fallback: search default runs dir
        labels_dir = find_latest_labels_dir(base_runs="runs")
    print("Autolabel labels_dir found at:", labels_dir)

    # Step B: Move generated .txt label files into our yolo dataset labels/
    moved = move_autolabels_to_dataset(labels_dir, yolo_type_path)
    print(f"Moved {moved} labels into {yolo_type_path} labels/ folders.")

    # Step C: Train final model (without --auto) using the newly generated labels
    final_project = "final_runs"
    final_name = f"final_{dataset_type.lower()}_{int(time.time())}"
    print(f"Starting final training for {dataset_type} (epochs={final_epochs}) ...")
    res2 = run_ultralytics_train(yaml_file, yolo_model_pretrained, final_epochs, imgsz,
                                 project=final_project, name=final_name)
    if res2.returncode != 0:
        print(f"Final training finished with non-zero exit code {res2.returncode}. Check logs.")
    else:
        print(f"Final training for {dataset_type} finished. Results under project={final_project}, name={final_name}")

    print(f"--- Completed {dataset_type} ---\n")
    return True

# -----------------------------
# RUN
# -----------------------------
if __name__ == "__main__":
    os.makedirs(output_path, exist_ok=True)
    try:
        for ds in datasets:
            process_dataset(ds)
        print("ALL DONE. Models saved in ultralytics run folders (project final_runs).")
    except Exception as e:
        print("Pipeline error:", e)
        raise
