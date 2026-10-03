import sys
import cv2
import numpy as np
import json
import os
from collections import Counter
import argparse
import traceback

# Add error logging
import logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def rgb_to_color_name(r, g, b):
    """Convert RGB values to color names"""
    colors = {
        'Black': [(0, 0, 0), (50, 50, 50)],
        'White': [(200, 200, 200), (255, 255, 255)],
        'Brown': [(101, 67, 33), (160, 120, 80)],
        'Light Brown': [(160, 120, 80), (200, 160, 120)],
        'Golden': [(255, 215, 0), (255, 255, 150)],
        'Grey': [(50, 50, 50), (200, 200, 200)],
        'Orange': [(255, 140, 0), (255, 200, 100)],
        'Red': [(150, 0, 0), (255, 100, 100)],
        'Yellow': [(200, 200, 0), (255, 255, 200)],
        'Cream': [(245, 245, 220), (255, 255, 240)],
        'Tan': [(210, 180, 140), (240, 220, 180)],
        'Silver': [(192, 192, 192), (220, 220, 220)],
    }
    
    min_distance = float('inf')
    closest_color = 'Mixed'
    
    for color_name, (min_rgb, max_rgb) in colors.items():
        if (min_rgb[0] <= r <= max_rgb[0] and 
            min_rgb[1] <= g <= max_rgb[1] and 
            min_rgb[2] <= b <= max_rgb[2]):
            return color_name
        
        center_r = (min_rgb[0] + max_rgb[0]) / 2
        center_g = (min_rgb[1] + max_rgb[1]) / 2
        center_b = (min_rgb[2] + max_rgb[2]) / 2
        
        distance = ((r - center_r) ** 2 + (g - center_g) ** 2 + (b - center_b) ** 2) ** 0.5
        
        if distance < min_distance:
            min_distance = distance
            closest_color = color_name
    
    return closest_color

def detect_dominant_colors(image_path, k=5):
    """Detect dominant colors in an image using K-means clustering"""
    try:
        logger.info(f"Processing image: {image_path}")
        
        if not os.path.exists(image_path):
            logger.error(f"Image file does not exist: {image_path}")
            return []
            
        if not os.access(image_path, os.R_OK):
            logger.error(f"Image file is not readable: {image_path}")
            return []
            
        image = cv2.imread(image_path)
        if image is None:
            logger.error(f"Could not load image: {image_path}")
            return []
            
        # Convert BGR to RGB
        image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        
        # Resize image for faster processing
        height, width = image.shape[:2]
        if width > 300:
            new_width = 300
            new_height = int(height * (new_width / width))
            image = cv2.resize(image, (new_width, new_height))
        
        # Reshape image to be a list of pixels
        pixels = image.reshape((-1, 3))
        
        # Remove very dark and very light pixels (noise reduction)
        mask = np.logical_and(
            np.sum(pixels, axis=1) > 30,
            np.sum(pixels, axis=1) < 700
        )
        filtered_pixels = pixels[mask]
        
        if len(filtered_pixels) < 10:
            filtered_pixels = pixels
        
        # Ensure we have enough pixels for clustering
        n_clusters = min(k, len(filtered_pixels))
        if n_clusters < 1:
            logger.warning(f"Not enough pixels for clustering in {image_path}")
            return []
            
        # Import sklearn here to catch import errors
        try:
            from sklearn.cluster import KMeans
        except ImportError as e:
            logger.error(f"sklearn not available: {e}")
            return []
            
        # Apply K-means clustering
        kmeans = KMeans(n_clusters=n_clusters, random_state=42, n_init=10)
        kmeans.fit(filtered_pixels)
        
        # Get the colors and their frequencies
        colors = kmeans.cluster_centers_
        labels = kmeans.labels_
        
        # Count frequency of each cluster
        label_counts = Counter(labels)
        
        # Sort colors by frequency
        color_frequencies = []
        for i, color in enumerate(colors):
            frequency = label_counts[i] / len(labels)
            color_frequencies.append((color, frequency))
        
        color_frequencies.sort(key=lambda x: x[1], reverse=True)
        
        # Convert to color names
        color_names = []
        for color, frequency in color_frequencies:
            if frequency > 0.05:
                color_name = rgb_to_color_name(int(color[0]), int(color[1]), int(color[2]))
                if color_name and color_name not in color_names:
                    color_names.append(color_name)
        
        logger.info(f"Detected colors for {image_path}: {color_names}")
        return color_names[:3]
        
    except Exception as e:
        logger.error(f"Error in detect_dominant_colors for {image_path}: {str(e)}")
        logger.error(traceback.format_exc())
        return []

def analyze_multiple_images(image_paths):
    """Analyze multiple images and return consolidated color results"""
    try:
        all_colors = []
        individual_results = []
        
        for image_path in image_paths:
            colors = detect_dominant_colors(image_path)
            
            individual_results.append({
                'image_path': image_path,
                'colors': colors
            })
            
            all_colors.extend(colors)
        
        # Count color frequency across all images
        color_counter = Counter(all_colors)
        
        # Get most common colors
        most_common_colors = [color for color, count in color_counter.most_common(3)]
        
        return {
            'success': True,
            'colors': most_common_colors,
            'individual_results': individual_results,
            'color_frequency': dict(color_counter)
        }
        
    except Exception as e:
        logger.error(f"Error in analyze_multiple_images: {str(e)}")
        logger.error(traceback.format_exc())
        return {
            'success': False,
            'error': str(e),
            'message': 'Multiple image color analysis failed'
        }

def main():
    try:
        # Check if we have command line arguments
        if len(sys.argv) < 2:
            raise ValueError("No image paths provided")
            
        image_paths = sys.argv[1:]
        
        logger.info(f"Processing {len(image_paths)} images")
        
        # Validate all image paths exist
        for image_path in image_paths:
            if not os.path.exists(image_path):
                raise FileNotFoundError(f"Image file not found: {image_path}")
        
        if len(image_paths) == 1:
            colors = detect_dominant_colors(image_paths[0])
            
            result = {
                'success': True,
                'colors': colors,
                'image_path': image_paths[0]
            }
        else:
            result = analyze_multiple_images(image_paths)
        
        # Ensure we always return valid JSON
        print(json.dumps(result, indent=2))
        
    except Exception as e:
        logger.error(f"Main function error: {str(e)}")
        logger.error(traceback.format_exc())
        
        error_result = {
            'success': False,
            'error': str(e),
            'traceback': traceback.format_exc(),
            'message': 'Color detection failed'
        }
        print(json.dumps(error_result, indent=2))
        sys.exit(1)

if __name__ == "__main__":
    main()