import sys
import os
sys.path.append(os.path.dirname(__file__))

from color_detection import detect_dominant_colors, analyze_multiple_images

# Test single image
if len(sys.argv) > 1:
    image_path = sys.argv[1]
    colors = detect_dominant_colors(image_path)
    print(f"Detected colors: {colors}")

# Test multiple images
if len(sys.argv) > 2:
    image_paths = sys.argv[1:]
    result = analyze_multiple_images(image_paths)
    print(f"Multiple image analysis: {result}")

    