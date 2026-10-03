import os
import shutil

# -----------------------------
# CONFIGURATION
# -----------------------------
dataset_path = "/home/pohling/Pet-Adoption-and-Care-system/yolo_datasett"

SPECIES_MAPPING = {
    "dog": [
        "american_bulldog",
        "american_pit_bull_terrier",
        "basset_hound",
        "beagle",
        "border_collies",
        "boxer",
        "chihuahua",
        "corgi",
        "dachshund",
        "english_cocker_spaniel",
        "english_setter",
        "french_bulldog",
        "german shepherd",
        "german_shorthaired",
        "golden retriever",
        "great_pyrenees",
        "havanese",
        "husky",
        "japanese_chin",
        "keeshond",
        "labrador",
        "leonberger",
        "maltese",
        "miniature_pinscher",
        "newfoundland",
        "pomeranian",
        "poodle",
        "pug",
        "rottwiler",
        "saint_bernard",
        "samoyed",
        "scottish_terrier",
        "shiba_inu",
        "shih_tzu",
        "staffordshire_bull_terrier",
        "standard_poodle",
        "wheaten_terrier",
        "yorkshire_terrier",
    ],
    "cat": [
        "Abyssinian",
        "American_Shorthair",
        "Bengal",
        "Birman",
        "Bombay",
        "British_Shorthair",
        "Domestic_Short_Hair",
        "Egyptian_Mau",
        "Himalaya",
        "Maine_Coon",
        "Persian",
        "Ragdoll",
        "Russian_Blue",
        "Siamese",
        "Sphynx",
    ]
}

# -----------------------------
# AUTOMATED SETUP
# -----------------------------
def organize_and_build_species():
    breed_dir = os.path.join(dataset_path, "breed")
    species_dir = os.path.join(dataset_path, "species")

    # Create the breed and species folders
    os.makedirs(breed_dir, exist_ok=True)
    os.makedirs(species_dir, exist_ok=True)

    # Reverse mapping: { "husky": "dog", "persian": "cat" }
    breed_to_species = {}
    for species, breeds in SPECIES_MAPPING.items():
        for breed in breeds:
            breed_to_species[breed] = species

    print(f"Scanning directory: {dataset_path}\n")

    # Loop through everything inside yolo_datasett
    for item in os.listdir(dataset_path):
        # Skip the newly created 'breed' and 'species' folders
        if item in ["breed", "species"]:
            continue

        item_path = os.path.join(dataset_path, item)
        
        # We only care about folders
        if not os.path.isdir(item_path):
            continue

        breed_name = item
        species_name = breed_to_species.get(breed_name)

        if not species_name:
            print(f"⚠️ Warning: Folder '{breed_name}' is not in your SPECIES_MAPPING. Skipping...")
            continue

        # 1. MOVE the breed folder inside the 'breed' folder
        new_breed_path = os.path.join(breed_dir, breed_name)
        shutil.move(item_path, new_breed_path)
        print(f"📁 Moved '{breed_name}' into 'breed' folder.")

        # 2. COPY the images to the 'species' folder
        target_species_path = os.path.join(species_dir, species_name)
        os.makedirs(target_species_path, exist_ok=True)
        
        images = [f for f in os.listdir(new_breed_path) if f.lower().endswith(('.png', '.jpg', '.jpeg'))]
        
        for filename in images:
            src_file = os.path.join(new_breed_path, filename)
            
            # Prefix filename to avoid overwriting images with the same name
            safe_filename = f"{breed_name}_{filename}"
            dst_file = os.path.join(target_species_path, safe_filename)
            
            shutil.copy(src_file, dst_file)
            
        print(f"   -> Copied {len(images)} images to 'species/{species_name}'.")

    print("\n✅ Dataset successfully organized and species dataset generated!")

if __name__ == "__main__":
    organize_and_build_species()