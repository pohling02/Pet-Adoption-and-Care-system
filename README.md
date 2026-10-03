# Petopia Adoption and Care System

Petopia is a full-stack web application developed with Laravel and Python. It is designed to digitize the pet adoption pipeline, focusing on transparency, automated data entry, and intelligent matchmaking between adopters and animals.

## User Roles

* Adopter: Browses available pets, submits applications, manages household care plans, and tracks adoption status in real-time.
* Shelter Staff: Manages pet profiles, reviews applications, and maintains veterinary documentation including vaccination schedules and health records.
* Admin: Oversees system-wide operations, manages user accounts, and handles high-level data administration.

### Design Consideration: Veterinary Integration
In the current version of the system, veterinary functions are integrated into the Shelter Staff role. This design decision acknowledges the operational reality of many NGOs where volunteer veterinarians or medical coordinators often operate as part of the core shelter team. While a separate Doctor role is planned for future iterations to allow for more granular medical permissions, the current consolidation ensures a streamlined workflow for health record management within a volunteer-driven environment.

## Advanced AI Architecture

The application replaces manual trait entry with automated AI modules to ensure data consistency and provide semantic search capabilities.

### 1. Computer Vision (Python and YOLOv8)
* Majority Vote Breed Detection: To mitigate the impact of poor image quality, the system analyzes multiple photos per pet using a yolov8n-cls model. A Majority Vote algorithm is applied to confirm the breed, requiring a 0.60 confidence threshold to ensure accuracy.
* Dominant Color Extraction: Using K-Means Clustering via OpenCV and Scikit-learn, the system identifies the primary colors of a pet's coat. These are mapped to standardized labels such as Golden, Tan, or Black to enable precise search filtering.

### 2. Semantic Matching (OpenAI Embeddings)
* Semantic Search: The system utilizes the text-embedding-3-large model to transform adopter preferences and pet personalities into 1536-dimension vectors.
* Cosine Similarity: Instead of basic keyword matching, the system calculates the mathematical distance between these vectors to determine a compatibility score. This allows the system to understand that an active lifestyle is a conceptual match for an energetic breed.

## Key Features

* Real-time Status Tracking: Adopters can monitor their application progress through various stages such as Under Review, Approved, or Pending Resubmission.
* Consolidated Health Records: Staff can manage medical history and vaccination records directly within the pet's profile.
* Batch Image Processing: AI modules process multiple uploads simultaneously to identify breeds and colors automatically.
* Interactive UI: Modal-based review systems allow shelter staff to evaluate environment photos and adopter details without leaving the main dashboard.

## Technical Stack

* Backend: PHP 8.2 (Laravel Framework)
* Frontend: JavaScript, HTML, CSS
* Database: MySQL
* AI Engine: Python 3.x (Ultralytics, Scikit-learn, OpenCV, OpenAI API)
* Architecture: Model-View-Controller (MVC)

## Installation and Setup

### 1. Prerequisites
* PHP 8.2+, MySQL 8.0+, Python 3.10+, Composer

### 2. Backend Installation
```bash
# Install PHP extensions and dependencies
sudo apt install php8.2-mbstring php8.2-mysql
COMPOSER_MEMORY_LIMIT=2G composer install
```

### 3. Database Configuration
```bash
# Setup local database
sudo service mysql start
sudo mysql -u root -e "CREATE DATABASE pet_db; 
CREATE USER 'YOUR_USERNAME'@'localhost' IDENTIFIED BY 'YOUR_SECURE_PASSWORD'; 
GRANT ALL PRIVILEGES ON pet_db.* TO 'YOUR_USERNAME'@'localhost'; 
FLUSH PRIVILEGES;"
```

### 4. Environment Variables
Create a .env file and update your local database credentials and OpenAI API key:
```bash
DB_DATABASE=pet_db
DB_USERNAME=YOUR_USERNAME
DB_PASSWORD=YOUR_SECURE_PASSWORD
OPENAI_API_KEY=your_api_key_here
```

### 5. Python AI Module Setup
The required Python environment is pre-configured. Use the provided requirements file to install all dependencies:

```bash
pip install -r requirements.txt
```

## Known Limitations and Future Roadmap
* Granular Role Separation: Splitting the Staff role into a dedicated Veterinary Doctor portal for advanced medical management.

* Notification System: Integration of real-time SMS and Email alerts for application status updates.

* Mobile Port: Developing a native mobile interface for field volunteers to update pet records on-site.
