<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\PetImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PetManagementController extends Controller
{

    public function managePets()
    {
        $shelterStaffID = Auth::id();

        $pets = Pet::where('ShelterID', $shelterStaffID)
            ->when(request()->search, function ($query) {
                $query->where('PetName', 'like', '%' . request()->search . '%')
                    ->orWhere('Species', 'like', '%' . request()->search . '%')
                    ->orWhere('Breed', 'like', '%' . request()->search . '%');
            })
            ->orderBy('PetName', 'asc')
            ->paginate(10);

        return view('ShelterStaff.pets.manage_pets', compact('pets'));
    }

    public function view($id)
    {
        $pet = Pet::findOrFail($id);
        return view('ShelterStaff.pets.pet_details', compact('pet'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'shelter_staff') {
            return redirect()->route('shelter.pets.manage')->with('error', 'You are not authorized to add pets until approved.');
        }
        return view('ShelterStaff.pets.add_pet');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'shelter_staff') {
            return redirect()->route('shelter.pets.manage')->with('error', 'You are not authorized to add pets until approved.');
        }

        $validatedData = $request->validate([
            'PetName' => 'required|string|max:255',
            'Species' => 'required|string|max:255',
            'Breed' => 'required_without:ManualBreed',
            'ManualBreed' => 'required_without:Breed|string|max:255',
            'Color' => 'nullable|array',
            'Color.*' => 'string|in:Black,White,Brown,Light Brown,Golden,Grey,Orange,Black & White,Brindle,Tabby,Calico,Mixed,Other',
            'Gender' => 'required|string',
            'DateOfBirth' => 'required|date',
            'AdoptionStatus' => 'required|string',
            'Personality' => 'nullable|string|max:1000',
            'Background' => 'nullable|string|max:1000',
            'Neutering' => 'required|boolean',
            'EnergyLevel' => 'required|integer|min:1|max:5',
            'Appetite' => 'required|integer|min:1|max:5',
            'Friendliness' => 'required|integer|min:1|max:5',
            'Adaptability' => 'required|integer|min:1|max:5',
            'Allergy' => 'required|boolean',
            'AllergyDetails' => 'nullable|string|max:255',
            'CurrentLocation' => 'required|string|max:255',
            'CurrentAddress' => 'required|string|max:255',
            'VaccinationStatus' => 'required|string|max:255',
            'HealthCondition' => 'nullable|string|max:255',
            'SpecialNeed' => 'nullable|string|max:1000',
            'PetImages.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $pet = Pet::create([
                'PetName' => $validatedData['PetName'],
                'Species' => $validatedData['Species'],
                'Breed' => $validatedData['Breed'],
                'ManualBreed' => $validatedData['ManualBreed'],
                'Color' => $validatedData['Color'] ? implode(', ', $validatedData['Color']) : null,
                'Gender' => $validatedData['Gender'],
                'DateOfBirth' => $validatedData['DateOfBirth'],
                'AdoptionStatus' => $validatedData['AdoptionStatus'],
                'Personality' => $validatedData['Personality'],
                'Background' => $validatedData['Background'],
                'Neutering' => $validatedData['Neutering'],
                'EnergyLevel' => $validatedData['EnergyLevel'],
                'Appetite' => $validatedData['Appetite'],
                'Friendliness' => $validatedData['Friendliness'],
                'Adaptability' => $validatedData['Adaptability'],
                'Allergy' => $validatedData['Allergy'],
                'AllergyDetails' => $validatedData['Allergy'] ? $validatedData['AllergyDetails'] : null,
                'CurrentLocation' => $validatedData['CurrentLocation'],
                'CurrentAddress' => $validatedData['CurrentAddress'],
                'VaccinationStatus' => $validatedData['VaccinationStatus'],
                'HealthCondition' => $validatedData['HealthCondition'] ?? null,
                'SpecialNeed' => $validatedData['SpecialNeed'] ?? null,
                'ShelterID' => auth()->id(),
            ]);

            if ($request->hasFile('PetImages')) {
                foreach ($request->file('PetImages') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images'), $imageName);
                    $pet->images()->create(['ImagePath' => 'images/' . $imageName]);
                }
            }
            return redirect()->route('shelter.pets.manage')->with('success', 'Pet added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while saving. Please try again.');
        }
    }

    public function edit($id)
    {
        $pet = Pet::with('images')->where('PetID', $id)
            ->where('ShelterID', auth()->id())
            ->firstOrFail();
        return view('ShelterStaff.pets.edit_pet', compact('pet'));
    }

    public function deleteImage($id)
    {
        Log::info('Delete request received for Image ID: ' . $id);
        $image = PetImage::findOrFail($id);

        if ($image->ImagePath) {
            Storage::delete('public/' . $image->ImagePath);
        }
        $image->delete();
        Log::info('✅ Image deleted successfully.');
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        Log::info('🐞 Update request received');

        $pet = Pet::where('PetID', $id)->where('ShelterID', auth()->id())->firstOrFail();

        $validatedData = $request->validate([
            'PetName' => 'required|string|max:255',
            'Species' => 'required|string|max:255',
            'Breed' => 'required_without:ManualBreed',
            'ManualBreed' => 'required_without:Breed|string|max:255',
            'Color' => 'nullable|array',
            'Color.*' => 'string|in:Black,White,Brown,Light Brown,Golden,Grey,Orange,Black & White,Brindle,Tabby,Calico,Mixed,Other',
            'Gender' => 'required|string',
            'DateOfBirth' => 'required|date',
            'AdoptionStatus' => 'required|string',
            'Personality' => 'nullable|string|max:1000',
            'Background' => 'nullable|string|max:1000',
            'Neutering' => 'required|boolean',
            'EnergyLevel' => 'required|integer|min:1|max:5',
            'Appetite' => 'required|integer|min:1|max:5',
            'Friendliness' => 'required|integer|min:1|max:5',
            'Adaptability' => 'required|integer|min:1|max:5',
            'Allergy' => 'required|boolean',
            'AllergyDetails' => 'nullable|string|max:255',
            'CurrentLocation' => 'required|string|max:255',
            'CurrentAddress' => 'required|string|max:255',
            'VaccinationStatus' => 'required|string|max:255',
            'HealthCondition' => 'nullable|string|max:255',
            'SpecialNeed' => 'nullable|string|max:1000',
            'PetImages.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'AIDetected' => 'nullable|boolean', // Track if breed was AI detected
        ]);

        Log::info('✅ Validation successful');

        // Handle Breed field (either from dropdown or manual input)
        if (!empty($validatedData['ManualBreed'])) {
            $validatedData['Breed'] = $validatedData['ManualBreed'];
        }
        unset($validatedData['ManualBreed']);

        $pet->fill($validatedData);
        $pet->AllergyDetails = $validatedData['Allergy'] ? $validatedData['AllergyDetails'] : null;

        if ($pet->isDirty()) {
            $pet->save();
            Log::info('✅ Pet details updated.', ['PetID' => $id]);
        }

        // Handle new image uploads
        if ($request->hasFile('PetImages')) {
            $uploadedImages = [];

            foreach ($request->file('PetImages') as $image) {
                if (!$image->isValid()) {
                    Log::error('🚨 Invalid image file detected.');
                    return back()->with('error', 'Invalid image file.');
                }

                $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $pet->images()->create(['ImagePath' => 'images/' . $imageName]);

                $uploadedImages[] = $imageName;
            }

            Log::info('✅ New pet images uploaded.', ['images' => $uploadedImages]);
        }

        return redirect()->route('shelter.pets.manage')->with('success', 'Pet updated successfully!');
    }

    /**
     * Detect breed from multiple images (can be called separately if needed)
     */
    public function detectBreedFromImages($imagePaths)
    {
        try {
            $pythonScript = "/home/pohling/Pet-Adoption-and-Care-system/scripts/breed_detect.py";
            $escapedPaths = array_map('escapeshellarg', $imagePaths);
            $command = "python3 " . escapeshellarg($pythonScript) . " " . implode(' ', $escapedPaths) . " 2>&1";

            Log::info('Running breed detection: ' . $command);
            $output = shell_exec($command);

            if (!$output) {
                return null;
            }

            $result = json_decode($output, true);

            if ($result && isset($result['success']) && $result['success']) {
                return $result;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Breed detection error: ' . $e->getMessage());
            return null;
        }
    }

    public function destroy($id)
    {
        $pet = Pet::where('PetID', $id)
            ->where('ShelterID', auth()->id())
            ->firstOrFail();
        $pet->delete();
        return redirect()->route('shelter.pets.manage')
            ->with('success', 'Pet deleted.');
    }

    public function detectAI(Request $request)
    {
        if (!$request->hasFile('images')) {
            return response()->json(['success' => false, 'error' => 'No image uploaded']);
        }

        try {
            $files = $request->file('images');
            
            $http = Http::asMultipart();

            foreach ($files as $file) {
                $http->attach(
                    'files', // 对应 FastAPI 的接收参数名 files
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }

            Log::info('Sending multipart request to FastAPI breed detection service...');
            
            // 发送请求到 Python 容器
            $response = $http->post('http://petopia-python:8000/api/v1/predict-breed');

            if (!$response->successful()) {
                throw new \Exception("FastAPI Microservice returned HTTP status " . $response->status());
            }

            $result = $response->json();

            if (!$result || !isset($result['success']) || !$result['success']) {
                $error = $result['error'] ?? 'AI Microservice detection failed';
                return response()->json(['success' => false, 'error' => $error]);
            }

            // 完全对齐并保留你原先返回给前端的 JSON 数据格式结构 ───
            if (!isset($result['final_prediction'])) {
                // 单张图片返回格式
                return response()->json([
                    'success' => true,
                    'species' => $result['species'],
                    'breed' => $result['prediction'],
                    'confidence' => isset($result['confidence']) ? round($result['confidence'] * 100, 2) : null,
                    'individual_predictions' => array_map(function ($pred) {
                        return [
                            'prediction' => $pred['prediction'],
                            'confidence' => round($pred['confidence'] * 100, 2),
                            'species' => $pred['species']
                        ];
                    }, $result['individual_predictions'])
                ]);
            }

            // 多张图片（多数投票）返回格式
            return response()->json([
                'success' => true,
                'final_prediction' => $result['final_prediction'],
                'final_species' => $result['final_species'],
                'individual_predictions' => array_map(function ($pred) {
                    return [
                        'prediction' => $pred['prediction'],
                        'confidence' => round($pred['confidence'] * 100, 2),
                        'species' => $pred['species']
                    ];
                }, $result['individual_predictions']),
                'vote_summary' => $result['vote_summary']
            ]);

        } catch (\Exception $e) {
            Log::error('FastAPI Breed Detection failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'AI Service connection error: ' . $e->getMessage()]);
        }
    }

    public function detectColors(Request $request)
    {
        Log::info('=== FastAPI Color Detection Request Started ===');

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        try {
            $files = $request->file('images');
            $http = Http::asMultipart();

            foreach ($files as $file) {
                $http->attach(
                    'files', 
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }

            // 轰出请求给 FastAPI 的毛色分析端
            $response = $http->post('http://petopia-python:8000/api/v1/predict-colors');

            if ($response->successful()) {
                $result = $response->json();
                $result['method'] = 'FastAPI + K-Means (Advanced Microservice)';
                Log::info('✅ FastAPI color detection successful');
                return response()->json($result);
            }

            // 备用降级方案（防错机制）：如果 Python 容器不小心挂了，降级用纯 PHP 跑
            Log::warning('FastAPI microservice unreachable, falling back to local PHP');
            $imagePaths = [];
            foreach ($files as $index => $image) {
                $tempPath = $image->storeAs('temp', 'color_fb_' . time() . '.' . $image->getClientOriginalExtension(), 'public');
                $imagePaths[] = storage_path('app/public/' . $tempPath);
            }
            $result = $this->detectColorsWithPHP($imagePaths);
            $result['method'] = 'PHP Local (Fallback)';
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Color detection failed completely: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function runColorDetectionScript($imagePaths)
    {
        try {
            // Validate input parameter
            if (empty($imagePaths) || !is_array($imagePaths)) {
                throw new \Exception("Invalid image paths provided to color detection script");
            }

            // Use base_path for better path resolution
            $scriptPath = base_path('scripts/color_detection.py');

            Log::info("Looking for Python script at: {$scriptPath}");

            if (!file_exists($scriptPath)) {
                throw new \Exception("Python color detection script not found at: " . $scriptPath);
            }

            // Validate all image paths exist
            foreach ($imagePaths as $imagePath) {
                if (!file_exists($imagePath)) {
                    throw new \Exception("Image file not found: {$imagePath}");
                }
            }

            // Build command array
            $command = ['python3', $scriptPath];
            $command = array_merge($command, $imagePaths);

            Log::info('Running color detection command: ' . implode(' ', array_map('escapeshellarg', $command)));

            $process = new Process($command);
            $process->setTimeout(120);
            $process->setWorkingDirectory(base_path());
            $process->run();

            $stdout = $process->getOutput();
            $stderr = $process->getErrorOutput();

            Log::info("Color Detection Python STDOUT: " . $stdout);
            if ($stderr) {
                Log::error("Color Detection Python STDERR: " . $stderr);
            }

            if (!$process->isSuccessful()) {
                Log::error('Color detection process failed with exit code: ' . $process->getExitCode());
                throw new ProcessFailedException($process);
            }

            if (empty($stdout)) {
                throw new \Exception("No output from color detection Python script");
            }

            $result = json_decode($stdout, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Invalid JSON from color detection Python script: " . $stdout);
                throw new \Exception("Invalid JSON from color detection Python: " . json_last_error_msg());
            }

            Log::info('Color detection result: ' . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            Log::error('runColorDetectionScript error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function detectColorsWithPHP($imagePaths)
    {
        Log::info('Starting enhanced PHP color detection for ' . count($imagePaths) . ' images');

        $allColors = [];
        $individualResults = [];

        foreach ($imagePaths as $imagePath) {
            try {
                $colors = $this->analyzeImageColorsAdvanced($imagePath);
                $individualResults[] = [
                    'image_path' => basename($imagePath),
                    'colors' => $colors
                ];
                $allColors = array_merge($allColors, $colors);
                Log::info("Analyzed colors for " . basename($imagePath) . ": " . implode(', ', $colors));
            } catch (\Exception $e) {
                Log::warning("Could not analyze colors for: " . $imagePath . " - " . $e->getMessage());
            }
        }

        if (empty($allColors)) {
            return [
                'success' => true,
                'colors' => ['Mixed'],
                'individual_results' => $individualResults,
                'color_frequency' => ['Mixed' => 1],
                'message' => 'Could not detect specific colors, defaulting to Mixed'
            ];
        }

        // Count frequency and return most common
        $colorCounts = array_count_values($allColors);
        arsort($colorCounts);

        $topColors = array_keys(array_slice($colorCounts, 0, 3, true));

        return [
            'success' => true,
            'colors' => $topColors,
            'individual_results' => $individualResults,
            'color_frequency' => $colorCounts
        ];
    }

    private function analyzeImageColorsAdvanced($imagePath)
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('GD extension is not available');
        }

        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            throw new \Exception("Could not get image info for: " . $imagePath);
        }

        // Create image resource
        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imagePath);
                break;
            default:
                throw new \Exception("Unsupported image type");
        }

        if (!$image) {
            throw new \Exception("Could not create image resource");
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Resize image for faster processing while maintaining aspect ratio
        $maxDimension = 400;
        if ($width > $maxDimension || $height > $maxDimension) {
            $ratio = min($maxDimension / $width, $maxDimension / $height);
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
            $width = $newWidth;
            $height = $newHeight;
        }

        // Extract all pixel colors
        $pixels = [];
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // Skip very dark and very light pixels (likely shadows/highlights)
                $brightness = ($r + $g + $b) / 3;
                if ($brightness > 20 && $brightness < 235) {
                    $pixels[] = [$r, $g, $b];
                }
            }
        }

        imagedestroy($image);

        if (empty($pixels)) {
            return ['Mixed'];
        }

        // Apply K-means clustering simulation
        $dominantColors = $this->kMeansColorClustering($pixels, 5);

        // Convert RGB clusters to color names
        $colorNames = [];
        foreach ($dominantColors as $color) {
            $colorName = $this->rgbToColorNameAdvanced($color[0], $color[1], $color[2]);
            if ($colorName && !in_array($colorName, $colorNames)) {
                $colorNames[] = $colorName;
            }
        }

        return array_slice($colorNames, 0, 3);
    }

    private function kMeansColorClustering($pixels, $k = 5)
    {
        if (count($pixels) < $k) {
            return $pixels;
        }

        // Initialize centroids randomly
        $centroids = [];
        $pixelCount = count($pixels);
        for ($i = 0; $i < $k; $i++) {
            $centroids[] = $pixels[rand(0, $pixelCount - 1)];
        }

        $maxIterations = 20;
        $tolerance = 1.0;

        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $clusters = array_fill(0, $k, []);

            // Assign pixels to nearest centroid
            foreach ($pixels as $pixel) {
                $minDistance = PHP_FLOAT_MAX;
                $closestCentroid = 0;

                for ($c = 0; $c < $k; $c++) {
                    $distance = $this->colorDistance($pixel, $centroids[$c]);
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $closestCentroid = $c;
                    }
                }

                $clusters[$closestCentroid][] = $pixel;
            }

            // Update centroids
            $newCentroids = [];
            $totalMovement = 0;

            for ($c = 0; $c < $k; $c++) {
                if (empty($clusters[$c])) {
                    $newCentroids[] = $centroids[$c];
                    continue;
                }

                $sumR = $sumG = $sumB = 0;
                $count = count($clusters[$c]);

                foreach ($clusters[$c] as $pixel) {
                    $sumR += $pixel[0];
                    $sumG += $pixel[1];
                    $sumB += $pixel[2];
                }

                $newCentroid = [
                    intval($sumR / $count),
                    intval($sumG / $count),
                    intval($sumB / $count)
                ];

                $totalMovement += $this->colorDistance($centroids[$c], $newCentroid);
                $newCentroids[] = $newCentroid;
            }

            $centroids = $newCentroids;

            // Check for convergence
            if ($totalMovement < $tolerance) {
                break;
            }
        }

        // Sort centroids by cluster size (most dominant first)
        $centroidSizes = [];
        for ($c = 0; $c < $k; $c++) {
            $centroidSizes[] = [
                'centroid' => $centroids[$c],
                'size' => count($clusters[$c])
            ];
        }

        usort($centroidSizes, function ($a, $b) {
            return $b['size'] - $a['size'];
        });

        return array_column($centroidSizes, 'centroid');
    }

    private function colorDistance($color1, $color2)
    {
        // Use weighted Euclidean distance (human eye is more sensitive to green)
        $rDiff = $color1[0] - $color2[0];
        $gDiff = $color1[1] - $color2[1];
        $bDiff = $color1[2] - $color2[2];

        return sqrt(2 * $rDiff * $rDiff + 4 * $gDiff * $gDiff + 3 * $bDiff * $bDiff);
    }

    private function rgbToColorNameAdvanced($r, $g, $b)
    {
        // Convert RGB to HSV for better color classification
        $hsv = $this->rgbToHsv($r, $g, $b);
        $hue = $hsv[0];
        $saturation = $hsv[1];
        $value = $hsv[2];

        // Low saturation = grayscale colors
        if ($saturation < 0.15) {
            if ($value < 0.2) return 'Black';
            if ($value > 0.8) return 'White';
            if ($value > 0.6) return 'Silver';
            return 'Grey';
        }

        // Low value = dark colors
        if ($value < 0.3) {
            return 'Black';
        }

        // High value, low saturation = light colors
        if ($value > 0.8 && $saturation < 0.3) {
            if ($hue >= 30 && $hue <= 60) return 'Cream';
            return 'White';
        }

        // Color classification based on hue
        if ($hue >= 0 && $hue < 15) return 'Red';
        if ($hue >= 15 && $hue < 45) {
            // Check if it's more brown or orange
            if ($value < 0.6 || $saturation < 0.5) return 'Brown';
            return 'Orange';
        }
        if ($hue >= 45 && $hue < 75) {
            if ($saturation > 0.7 && $value > 0.7) return 'Golden';
            if ($value < 0.5) return 'Brown';
            return 'Yellow';
        }
        if ($hue >= 75 && $hue < 150) return 'Green';
        if ($hue >= 150 && $hue < 210) return 'Blue';
        if ($hue >= 210 && $hue < 270) return 'Purple';
        if ($hue >= 270 && $hue < 330) return 'Pink';
        if ($hue >= 330 && $hue <= 360) return 'Red';

        // Additional brown detection for low saturation warm colors
        if ($hue >= 20 && $hue <= 60 && $saturation >= 0.2 && $value <= 0.7) {
            if ($value > 0.5) return 'Light Brown';
            return 'Brown';
        }

        // Tan detection
        if ($hue >= 30 && $hue <= 60 && $saturation >= 0.2 && $saturation <= 0.6 && $value >= 0.6) {
            return 'Tan';
        }

        return 'Mixed';
    }

    private function rgbToHsv($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $diff = $max - $min;

        // Value
        $v = $max;

        // Saturation
        $s = ($max == 0) ? 0 : $diff / $max;

        // Hue
        if ($diff == 0) {
            $h = 0;
        } else {
            switch ($max) {
                case $r:
                    $h = (60 * (($g - $b) / $diff) + 360) % 360;
                    break;
                case $g:
                    $h = (60 * (($b - $r) / $diff) + 120) % 360;
                    break;
                case $b:
                    $h = (60 * (($r - $g) / $diff) + 240) % 360;
                    break;
            }
        }

        return [$h, $s, $v];
    }
}
