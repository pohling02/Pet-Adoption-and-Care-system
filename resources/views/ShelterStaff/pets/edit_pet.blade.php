@extends('layouts.shelter_master')

@section('title', 'Edit Pet')

@section('content')
@php
use Illuminate\Support\Str;
// Convert $pet->Color to an array if it's a comma-separated string
$colors = old('Color', is_array($pet->Color) ? $pet->Color : explode(',', $pet->Color));
@endphp

<div class="container mt-4">
    <h2 class="text-center text-white bg-warning p-3 rounded shadow-sm">Edit Pet</h2>

    <div class="card p-4 shadow-sm">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('shelter.pets.update', ['id' => $pet->PetID]) }}" method="POST" enctype="multipart/form-data" id="petForm" class="needs-validation" novalidate>
            @csrf
            @method('PUT')

            <!-- Image Upload Section - Now at the top like add form -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3 text-warning">
                        <i class="fas fa-camera me-2"></i>Pet Images & Breed Detection
                    </h4>
                    <div class="card border-warning">
                        <div class="card-body">
                            <!-- AI Disclaimer Alert -->
                            <div class="alert alert-warning border-warning mb-3" role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-exclamation-triangle text-warning me-2 mt-1"></i>
                                    <div>
                                        <strong>AI Detection Disclaimer:</strong>
                                        <p class="mb-1">Our AI breed detection is a helpful tool but may not be 100% accurate. Please:</p>
                                        <ul class="mb-0 small">
                                            <li>Always verify the detected breed information</li>
                                            <li>Use your professional judgment and experience</li>
                                            <li>Consider the pet's physical characteristics and behavior</li>
                                            <li>Consult with veterinarians for accurate breed identification when needed</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="PetImages" class="form-label fw-bold">
                                    Upload New Pet Images
                                    <span class="badge bg-info ms-2">AI Breed Detection Enabled</span>
                                </label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="PetImages[]" id="PetImages"
                                        onchange="handleImageUpload(this);" multiple accept="image/*" />
                                    <button class="btn btn-outline-warning" type="button"
                                        onclick="document.getElementById('PetImages').click()">
                                        <i class="fas fa-upload me-1"></i>Browse Images
                                    </button>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload one or multiple images (max 5 MB each). AI breed detection works with both
                                    single and multiple images.
                                    <br>For multiple images, the system will analyze all images and use majority voting
                                    for the final prediction.
                                    <br>Supported formats: JPG, PNG, GIF.
                                    <br><strong class="text-warning">⚠️ AI detection is for assistance only - please
                                        verify all information manually.</strong>
                                </div>
                                @error('PetImages')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- AI Detection Status -->
                            <div id="aiDetectionStatus" class="alert alert-info d-none" role="alert">
                                <i class="fas fa-robot me-2"></i>
                                <span id="detectionMessage">Analyzing image for breed detection...</span>
                            </div>

                            <!-- Image Preview Container -->
                            <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <h4 class="mb-3 text-warning">
                        <i class="fas fa-paw me-2"></i>Basic Information
                    </h4>

                    <!-- Pet Code Field -->
                    <div class="mb-3">
                        <label for="PetCode" class="form-label">Pet Code</label>
                        <input type="text" class="form-control @error('PetCode') is-invalid @enderror" id="PetCode"
                            name="PetCode" value="{{ old('PetCode', $pet->PetCode) }}" placeholder="Optional - Leave blank for auto-generation">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Leave blank to auto-generate a unique pet code, or enter a custom code.
                        </div>
                        @error('PetCode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="PetName" class="form-label">Pet Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('PetName') is-invalid @enderror" id="PetName"
                            name="PetName" value="{{ old('PetName', $pet->PetName) }}" required>
                        @error('PetName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Species" class="form-label">Species <span class="text-danger">*</span></label>
                                <select class="form-control @error('Species') is-invalid @enderror" id="Species"
                                    name="Species" required>
                                    <option value="">Select Species</option>
                                    <option value="Dog" {{ old('Species', $pet->Species) == 'Dog' ? 'selected' : '' }}>Dog</option>
                                    <option value="Cat" {{ old('Species', $pet->Species) == 'Cat' ? 'selected' : '' }}>Cat</option>
                                </select>
                                @error('Species')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Breed" class="form-label">
                                    Breed <span class="text-danger">*</span>
                                    <span id="aiDetectedBadge" class="badge bg-success ms-1 d-none">
                                        AI Detected - Please Verify
                                    </span>
                                </label>
                                <div class="input-group">
                                    <select class="form-control @error('Breed') is-invalid @enderror" id="Breed"
                                        name="Breed" required>
                                        <option value="">Select Breed</option>
                                        <optgroup label="Dog Breeds">
                                            <option value="American Bulldog" {{ old('Breed', $pet->Breed) == 'American Bulldog' ? 'selected' : '' }}>American Bulldog</option>
                                            <option value="American Pit Bull Terrier" {{ old('Breed', $pet->Breed) == 'American Pit Bull Terrier' ? 'selected' : '' }}>American Pit Bull Terrier</option>
                                            <option value="Basset Hound" {{ old('Breed', $pet->Breed) == 'Basset Hound' ? 'selected' : '' }}>Basset Hound</option>
                                            <option value="Beagle" {{ old('Breed', $pet->Breed) == 'Beagle' ? 'selected' : '' }}>Beagle</option>
                                            <option value="Boxer" {{ old('Breed', $pet->Breed) == 'Boxer' ? 'selected' : '' }}>Boxer</option>
                                            <option value="Border Collie" {{ old('Breed', $pet->Breed) == 'Border Collie' ? 'selected' : '' }}>Border Collie</option>
                                            <option value="Chihuahua" {{ old('Breed', $pet->Breed) == 'Chihuahua' ? 'selected' : '' }}>Chihuahua</option>
                                            <option value="Corgi" {{ old('Breed', $pet->Breed) == 'Corgi' ? 'selected' : '' }}>Corgi</option>
                                            <option value="Dachshund" {{ old('Breed', $pet->Breed) == 'Dachshund' ? 'selected' : '' }}>Dachshund</option>
                                            <option value="English Cocker Spaniel" {{ old('Breed', $pet->Breed) == 'English Cocker Spaniel' ? 'selected' : '' }}>English Cocker Spaniel</option>
                                            <option value="English Setter" {{ old('Breed', $pet->Breed) == 'English Setter' ? 'selected' : '' }}>English Setter</option>
                                            <option value="French Bulldog" {{ old('Breed', $pet->Breed) == 'French Bulldog' ? 'selected' : '' }}>French Bulldog</option>
                                            <option value="German Shepherd" {{ old('Breed', $pet->Breed) == 'German Shepherd' ? 'selected' : '' }}>German Shepherd</option>
                                            <option value="German Shorthaired Pointer" {{ old('Breed', $pet->Breed) == 'German Shorthaired Pointer' ? 'selected' : '' }}>German Shorthaired Pointer</option>
                                            <option value="Golden Retriever" {{ old('Breed', $pet->Breed) == 'Golden Retriever' ? 'selected' : '' }}>Golden Retriever</option>
                                            <option value="Great Pyrenees" {{ old('Breed', $pet->Breed) == 'Great Pyrenees' ? 'selected' : '' }}>Great Pyrenees</option>
                                            <option value="Havanese" {{ old('Breed', $pet->Breed) == 'Havanese' ? 'selected' : '' }}>Havanese</option>
                                            <option value="Husky" {{ old('Breed', $pet->Breed) == 'Husky' ? 'selected' : '' }}>Husky</option>
                                            <option value="Japanese Chin" {{ old('Breed', $pet->Breed) == 'Japanese Chin' ? 'selected' : '' }}>Japanese Chin</option>
                                            <option value="Keeshond" {{ old('Breed', $pet->Breed) == 'Keeshond' ? 'selected' : '' }}>Keeshond</option>
                                            <option value="Labrador Retriever" {{ old('Breed', $pet->Breed) == 'Labrador Retriever' ? 'selected' : '' }}>Labrador Retriever</option>
                                            <option value="Leonberger" {{ old('Breed', $pet->Breed) == 'Leonberger' ? 'selected' : '' }}>Leonberger</option>
                                            <option value="Maltese" {{ old('Breed', $pet->Breed) == 'Maltese' ? 'selected' : '' }}>Maltese</option>
                                            <option value="Miniature Pinscher" {{ old('Breed', $pet->Breed) == 'Miniature Pinscher' ? 'selected' : '' }}>Miniature Pinscher</option>
                                            <option value="Newfoundland" {{ old('Breed', $pet->Breed) == 'Newfoundland' ? 'selected' : '' }}>Newfoundland</option>
                                            <option value="Poodle" {{ old('Breed', $pet->Breed) == 'Poodle' ? 'selected' : '' }}>Poodle</option>
                                            <option value="Pomeranian" {{ old('Breed', $pet->Breed) == 'Pomeranian' ? 'selected' : '' }}>Pomeranian</option>
                                            <option value="Pug" {{ old('Breed', $pet->Breed) == 'Pug' ? 'selected' : '' }}>Pug</option>
                                            <option value="Rottweiler" {{ old('Breed', $pet->Breed) == 'Rottweiler' ? 'selected' : '' }}>Rottweiler</option>
                                            <option value="Saint Bernard" {{ old('Breed', $pet->Breed) == 'Saint Bernard' ? 'selected' : '' }}>Saint Bernard</option>
                                            <option value="Samoyed" {{ old('Breed', $pet->Breed) == 'Samoyed' ? 'selected' : '' }}>Samoyed</option>
                                            <option value="Shih Tzu" {{ old('Breed', $pet->Breed) == 'Shih Tzu' ? 'selected' : '' }}>Shih Tzu</option>
                                            <option value="Scottish Terrier" {{ old('Breed', $pet->Breed) == 'Scottish Terrier' ? 'selected' : '' }}>Scottish Terrier</option>
                                            <option value="Shiba Inu" {{ old('Breed', $pet->Breed) == 'Shiba Inu' ? 'selected' : '' }}>Shiba Inu</option>
                                            <option value="Staffordshire Bull Terrier" {{ old('Breed', $pet->Breed) == 'Staffordshire Bull Terrier' ? 'selected' : '' }}>Staffordshire Bull Terrier</option>
                                            <option value="Standard Poodle" {{ old('Breed', $pet->Breed) == 'Standard Poodle' ? 'selected' : '' }}>Standard Poodle</option>
                                            <option value="Wheaten Terrier" {{ old('Breed', $pet->Breed) == 'Wheaten Terrier' ? 'selected' : '' }}>Wheaten Terrier</option>
                                            <option value="Yorkshire Terrier" {{ old('Breed', $pet->Breed) == 'Yorkshire Terrier' ? 'selected' : '' }}>Yorkshire Terrier</option>
                                            <option value="Mixed Breed" {{ old('Breed', $pet->Breed) == 'Mixed Breed' ? 'selected' : '' }}>Mixed Breed</option>
                                            <option value="Other" {{ old('Breed', $pet->Breed) == 'Other' ? 'selected' : '' }}>Other (Specify below)</option>
                                        </optgroup>
                                        <optgroup label="Cat Breeds">
                                            <option value="Abyssinian" {{ old('Breed', $pet->Breed) == 'Abyssinian' ? 'selected' : '' }}>Abyssinian</option>
                                            <option value="American Short Hair" {{ old('Breed', $pet->Breed) == 'American Short Hair' ? 'selected' : '' }}>American Short Hair</option>
                                            <option value="Bengal" {{ old('Breed', $pet->Breed) == 'Bengal' ? 'selected' : '' }}>Bengal</option>
                                            <option value="Birman" {{ old('Breed', $pet->Breed) == 'Birman' ? 'selected' : '' }}>Birman</option>
                                            <option value="Bombay" {{ old('Breed', $pet->Breed) == 'Bombay' ? 'selected' : '' }}>Bombay</option>
                                            <option value="British Shorthair" {{ old('Breed', $pet->Breed) == 'British Shorthair' ? 'selected' : '' }}>British Shorthair</option>
                                            <option value="Domestic Short Hair" {{ old('Breed', $pet->Breed) == 'Domestic Short Hair' ? 'selected' : '' }}>Domestic Short Hair</option>
                                            <option value="Egyptian Mau" {{ old('Breed', $pet->Breed) == 'Egyptian Mau' ? 'selected' : '' }}>Egyptian Mau</option>
                                            <option value="Maine Coon" {{ old('Breed', $pet->Breed) == 'Maine Coon' ? 'selected' : '' }}>Maine Coon</option>
                                            <option value="Himalayan" {{ old('Breed', $pet->Breed) == 'Himalayan' ? 'selected' : '' }}>Himalayan</option>
                                            <option value="Persian" {{ old('Breed', $pet->Breed) == 'Persian' ? 'selected' : '' }}>Persian</option>
                                            <option value="Ragdoll" {{ old('Breed', $pet->Breed) == 'Ragdoll' ? 'selected' : '' }}>Ragdoll</option>
                                            <option value="Russian Blue" {{ old('Breed', $pet->Breed) == 'Russian Blue' ? 'selected' : '' }}>Russian Blue</option>
                                            <option value="Siamese" {{ old('Breed', $pet->Breed) == 'Siamese' ? 'selected' : '' }}>Siamese</option>
                                            <option value="Sphynx" {{ old('Breed', $pet->Breed) == 'Sphynx' ? 'selected' : '' }}>Sphynx</option>
                                            <option value="Other" {{ old('Breed', $pet->Breed) == 'Other' ? 'selected' : '' }}>Other (Specify below)</option>
                                        </optgroup>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleManualBreed"
                                        title="Enter custom breed">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>

                                <!-- Manual Breed Input (Hidden by default) -->
                                <div id="manualBreedContainer" class="mt-2" style="display: none;">
                                    <input type="text" class="form-control @error('ManualBreed') is-invalid @enderror"
                                        id="ManualBreed" name="ManualBreed" placeholder="Enter breed name manually"
                                        value="{{ old('ManualBreed', $pet->ManualBreed) }}">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Enter the breed name if it's not available in the dropdown list above.
                                    </div>
                                    @error('ManualBreed')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @error('Breed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Color" class="form-label">Color</label>
                                <select class="form-control @error('Color') is-invalid @enderror" id="Color"
                                    name="Color[]" multiple>
                                    <option value="Black" {{ (is_array($colors) && in_array('Black', $colors)) ? 'selected' : '' }}>Black</option>
                                    <option value="White" {{ (is_array($colors) && in_array('White', $colors)) ? 'selected' : '' }}>White</option>
                                    <option value="Brown" {{ (is_array($colors) && in_array('Brown', $colors)) ? 'selected' : '' }}>Brown</option>
                                    <option value="Light Brown" {{ (is_array($colors) && in_array('Light Brown', $colors)) ? 'selected' : '' }}>Light Brown</option>
                                    <option value="Golden" {{ (is_array($colors) && in_array('Golden', $colors)) ? 'selected' : '' }}>Golden</option>
                                    <option value="Grey" {{ (is_array($colors) && in_array('Grey', $colors)) ? 'selected' : '' }}>Grey</option>
                                    <option value="Orange" {{ (is_array($colors) && in_array('Orange', $colors)) ? 'selected' : '' }}>Orange / Ginger</option>
                                    <option value="Black & White" {{ (is_array($colors) && in_array('Black & White', $colors)) ? 'selected' : '' }}>Black & White</option>
                                    <option value="Brindle" {{ (is_array($colors) && in_array('Brindle', $colors)) ? 'selected' : '' }}>Brindle</option>
                                    <option value="Tabby" {{ (is_array($colors) && in_array('Tabby', $colors)) ? 'selected' : '' }}>Tabby</option>
                                    <option value="Calico" {{ (is_array($colors) && in_array('Calico', $colors)) ? 'selected' : '' }}>Calico</option>
                                    <option value="Mixed" {{ (is_array($colors) && in_array('Mixed', $colors)) ? 'selected' : '' }}>Mixed</option>
                                    <option value="Other" {{ (is_array($colors) && in_array('Other', $colors)) ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('Color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('Gender') is-invalid @enderror" id="Gender"
                                    name="Gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('Gender', $pet->Gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('Gender', $pet->Gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('Gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="DateOfBirth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control @error('DateOfBirth') is-invalid @enderror"
                                    id="DateOfBirth" name="DateOfBirth" value="{{ old('DateOfBirth', $pet->DateOfBirth) }}">
                                @error('DateOfBirth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CurrentLocation" class="form-label">Current State</label>
                                <select class="form-control @error('CurrentLocation') is-invalid @enderror"
                                    id="CurrentLocation" name="CurrentLocation">
                                    <option value="">Select State</option>
                                    <option value="Johor" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Johor' ? 'selected' : '' }}>Johor</option>
                                    <option value="Kedah" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                                    <option value="Kelantan" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                                    <option value="Melaka" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                                    <option value="Negeri Sembilan" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                                    <option value="Pahang" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                                    <option value="Perak" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Perak' ? 'selected' : '' }}>Perak</option>
                                    <option value="Perlis" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                                    <option value="Penang" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Penang' ? 'selected' : '' }}>Penang</option>
                                    <option value="Selangor" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                                    <option value="Terengganu" {{ old('CurrentLocation', $pet->CurrentLocation) == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                                </select>
                                @error('CurrentLocation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="CurrentAddress" class="form-label">Current Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('CurrentAddress') is-invalid @enderror"
                            id="CurrentAddress" name="CurrentAddress" value="{{ old('CurrentAddress', $pet->CurrentAddress) }}" required>
                        @error('CurrentAddress')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="AdoptionStatus" class="form-label">Adoption Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('AdoptionStatus') is-invalid @enderror" id="AdoptionStatus"
                            name="AdoptionStatus" required>
                            <option value="">Select Status</option>
                            <option value="Available" {{ old('AdoptionStatus', $pet->AdoptionStatus) == 'Available' ? 'selected' : '' }}>Available</option>
                            <option value="Pending" {{ old('AdoptionStatus', $pet->AdoptionStatus) == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Adopted" {{ old('AdoptionStatus', $pet->AdoptionStatus) == 'Adopted' ? 'selected' : '' }}>Adopted</option>
                        </select>
                        @error('AdoptionStatus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ideal Environment Field -->
                    <div class="mb-3">
                        <label for="Ideal_Environment" class="form-label">Ideal Environment <span class="text-danger">*</span></label>
                        <select class="form-select @error('Ideal_Environment') is-invalid @enderror" id="Ideal_Environment"
                            name="Ideal_Environment" required>
                            <option value="">Select Environment</option>
                            <option value="1" {{ old('Ideal_Environment', $pet->Ideal_Environment) == '1' ? 'selected' : '' }}>Apartment</option>
                            <option value="2" {{ old('Ideal_Environment', $pet->Ideal_Environment) == '2' ? 'selected' : '' }}>Landed Property</option>
                            <option value="3" {{ old('Ideal_Environment', $pet->Ideal_Environment) == '3' ? 'selected' : '' }}>Both Apartment & Landed</option>
                        </select>
                        @error('Ideal_Environment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="Personality" class="form-label">Personality</label>
                        <textarea class="form-control @error('Personality') is-invalid @enderror" id="Personality"
                            name="Personality" rows="3">{{ old('Personality', $pet->Personality) }}</textarea>
                        @error('Personality')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="Background" class="form-label">Background</label>
                        <textarea class="form-control @error('Background') is-invalid @enderror" id="Background"
                            name="Background" rows="3">{{ old('Background', $pet->Background) }}</textarea>
                        @error('Background')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <h4 class="mb-3 text-warning">
                        <i class="fas fa-heartbeat me-2"></i>Health & Characteristics
                    </h4>

                    <div class="mb-3">
                        <label for="HealthCondition" class="form-label">Health Condition</label>
                        <select class="form-control @error('HealthCondition') is-invalid @enderror" id="HealthCondition"
                            name="HealthCondition">
                            <option value="">Select a health condition</option>
                            <option value="Healthy" {{ strtolower(old('HealthCondition', $pet->HealthCondition)) == 'healthy' ? 'selected' : '' }}>Healthy</option>
                            <option value="Sick" {{ strtolower(old('HealthCondition', $pet->HealthCondition)) == 'sick' ? 'selected' : '' }}>Sick</option>
                            <option value="Critical" {{ strtolower(old('HealthCondition', $pet->HealthCondition)) == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('HealthCondition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="VaccinationStatus" class="form-label">Vaccination Status</label>
                        <select class="form-select @error('VaccinationStatus') is-invalid @enderror"
                            id="VaccinationStatus" name="VaccinationStatus">
                            <option value="">Select Status</option>
                            <option value="Fully Vaccinated" {{ old('VaccinationStatus', $pet->VaccinationStatus) == 'Fully Vaccinated' ? 'selected' : '' }}>Fully Vaccinated</option>
                            <option value="Partially Vaccinated" {{ old('VaccinationStatus', $pet->VaccinationStatus) == 'Partially Vaccinated' ? 'selected' : '' }}>Partially Vaccinated</option>
                            <option value="Not Vaccinated" {{ old('VaccinationStatus', $pet->VaccinationStatus) == 'Not Vaccinated' ? 'selected' : '' }}>Not Vaccinated</option>
                        </select>
                        @error('VaccinationStatus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Neutering</label>
                        <div class="btn-group" role="group" aria-label="Neutering options">
                            <input type="radio" class="btn-check" id="NeuteringYes" name="Neutering" value="1" {{ old('Neutering', $pet->Neutering) == 1 ? 'checked' : '' }}>
                            <label class="btn btn-outline-success" for="NeuteringYes">Yes</label>

                            <input type="radio" class="btn-check" id="NeuteringNo" name="Neutering" value="0" {{ old('Neutering', $pet->Neutering) == 0 ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger" for="NeuteringNo">No</label>
                        </div>
                        @error('Neutering')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Allergy</label>
                        <div class="btn-group" role="group" aria-label="Allergy options">
                            <input type="radio" class="btn-check" id="AllergyYes" name="Allergy" value="1"
                                onclick="toggleAllergyDetails(true)" {{ old('Allergy', $pet->Allergy) == 1 ? 'checked' : '' }}>
                            <label class="btn btn-outline-success" for="AllergyYes">Yes</label>

                            <input type="radio" class="btn-check" id="AllergyNo" name="Allergy" value="0"
                                onclick="toggleAllergyDetails(false)" {{ old('Allergy', $pet->Allergy) == 0 ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger" for="AllergyNo">No</label>
                        </div>
                        @error('Allergy')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="AllergyDetailsContainer"
                        style="{{ old('Allergy', $pet->Allergy) == 1 ? 'display:block' : 'display:none' }}">
                        <label for="AllergyDetails" class="form-label">Allergy To What?</label>
                        <input type="text" class="form-control @error('AllergyDetails') is-invalid @enderror"
                            id="AllergyDetails" name="AllergyDetails" value="{{ old('AllergyDetails', $pet->AllergyDetails ?? '') }}">
                        @error('AllergyDetails')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="SpecialNeed" class="form-label">Special Needs</label>
                        <textarea class="form-control @error('SpecialNeed') is-invalid @enderror" id="SpecialNeed"
                            name="SpecialNeed" rows="3">{{ old('SpecialNeed', $pet->SpecialNeed) }}</textarea>
                        @error('SpecialNeed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Trait Rating Section -->
                    <h5 class="mb-3 text-warning">
                        <i class="fas fa-star me-2"></i>Pet Traits (1-5 Scale)
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="EnergyLevel" class="form-label">Energy Level <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="range" class="form-range" id="EnergyLevel" name="EnergyLevel" min="1"
                                        max="5" value="{{ old('EnergyLevel', $pet->EnergyLevel) }}"
                                        oninput="updateRangeValue('EnergyLevelValue', this.value)" required>
                                    <span id="EnergyLevelValue"
                                        class="ms-2 badge bg-primary">{{ old('EnergyLevel', $pet->EnergyLevel) }}</span>
                                </div>
                                @error('EnergyLevel')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Appetite" class="form-label">Appetite <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="range" class="form-range" id="Appetite" name="Appetite" min="1" max="5"
                                        value="{{ old('Appetite', $pet->Appetite) }}"
                                        oninput="updateRangeValue('AppetiteValue', this.value)" required>
                                    <span id="AppetiteValue"
                                        class="ms-2 badge bg-primary">{{ old('Appetite', $pet->Appetite) }}</span>
                                </div>
                                @error('Appetite')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Friendliness" class="form-label">Friendliness <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="range" class="form-range" id="Friendliness" name="Friendliness" min="1"
                                        max="5" value="{{ old('Friendliness', $pet->Friendliness) }}"
                                        oninput="updateRangeValue('FriendlinessValue', this.value)" required>
                                    <span id="FriendlinessValue"
                                        class="ms-2 badge bg-primary">{{ old('Friendliness', $pet->Friendliness) }}</span>
                                </div>
                                @error('Friendliness')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Adaptability" class="form-label">Adaptability <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="range" class="form-range" id="Adaptability" name="Adaptability" min="1"
                                        max="5" value="{{ old('Adaptability', $pet->Adaptability) }}"
                                        oninput="updateRangeValue('AdaptabilityValue', this.value)" required>
                                    <span id="AdaptabilityValue"
                                        class="ms-2 badge bg-primary">{{ old('Adaptability', $pet->Adaptability) }}</span>
                                </div>
                                @error('Adaptability')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- New Trait Fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="BarkingLevel" class="form-label">Barking Level <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="range" class="form-range" id="BarkingLevel" name="BarkingLevel" min="1"
                                        max="5" value="{{ old('BarkingLevel', $pet->BarkingLevel) }}"
                                        oninput="updateRangeValue('BarkingLevelValue', this.value)" required>
                                    <span id="BarkingLevelValue"
                                        class="ms-2 badge bg-primary">{{ old('BarkingLevel', $pet->BarkingLevel) }}</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    1 = Very Quiet, 5 = Very Vocal
                                </div>
                                @error('BarkingLevel')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SheddingLevel" class="form-label">Shedding Level <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="range" class="form-range" id="SheddingLevel" name="SheddingLevel" min="1"
                                        max="5" value="{{ old('SheddingLevel', $pet->SheddingLevel) }}"
                                        oninput="updateRangeValue('SheddingLevelValue', this.value)" required>
                                    <span id="SheddingLevelValue"
                                        class="ms-2 badge bg-primary">{{ old('SheddingLevel', $pet->SheddingLevel) }}</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    1 = Minimal Shedding, 5 = Heavy Shedding
                                </div>
                                @error('SheddingLevel')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-warning btn-lg w-100">
                        <i class="fas fa-save me-2"></i> Update Pet
                    </button>
                </div>
            </div>
        </form>

        <!-- EXISTING IMAGES SECTION -->
        <div class="mt-5">
            <h4 class="mb-3 text-warning border-top pt-4">
                <i class="fas fa-images me-2"></i>Existing Images
            </h4>
            <div class="row" id="existingImages">
                @if(count($pet->images) > 0)
                    @foreach ($pet->images as $image)
                        <div class="col-md-3 col-sm-4 col-6 mb-3 image-container" data-image-id="{{ $image->ImageID }}">
                            <div class="card h-100 position-relative">
                                <img src="{{ asset($image->ImagePath) }}" class="card-img-top" alt="{{ $pet->PetName }}" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <button type="button" class="btn btn-danger btn-sm w-100 delete-image"
                                            data-image-id="{{ $image->ImageID }}">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No images uploaded yet.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables to track detection state
    let isDetectionInitialized = false;

    document.addEventListener('DOMContentLoaded', function () {
        const speciesSelect = document.getElementById('Species');
        const breedSelect = document.getElementById('Breed');
        const toggleManualBreedBtn = document.getElementById('toggleManualBreed');
        const manualBreedContainer = document.getElementById('manualBreedContainer');
        const manualBreedInput = document.getElementById('ManualBreed');

        // Null checks
        if (!speciesSelect || !breedSelect || !toggleManualBreedBtn || !manualBreedContainer || !manualBreedInput) {
            console.error('Required DOM elements not found');
            return;
        }

        // Identify each optgroup by its label
        const dogGroup = breedSelect.querySelector('optgroup[label="Dog Breeds"]');
        const catGroup = breedSelect.querySelector('optgroup[label="Cat Breeds"]');

        if (!dogGroup || !catGroup) {
            console.error('Breed optgroups not found');
            return;
        }

        function toggleBreedOptions() {
            const species = speciesSelect.value;

            if (species === 'Dog') {
                dogGroup.style.display = 'block';
                catGroup.style.display = 'none';

                // If a cat breed is currently selected, reset the breed
                if (catGroup.querySelector('option:checked')) {
                    breedSelect.value = '';
                }
            }
            else if (species === 'Cat') {
                dogGroup.style.display = 'none';
                catGroup.style.display = 'block';

                // If a dog breed is currently selected, reset the breed
                if (dogGroup.querySelector('option:checked')) {
                    breedSelect.value = '';
                }
            }
            else {
                // If no valid species is selected, hide both groups
                dogGroup.style.display = 'none';
                catGroup.style.display = 'none';
                breedSelect.value = '';
            }
        }

        // Toggle manual breed input - Only add listener once
        toggleManualBreedBtn.addEventListener('click', function () {
            const isVisible = manualBreedContainer.style.display !== 'none';

            if (isVisible) {
                // Hide manual input, show dropdown
                manualBreedContainer.style.display = 'none';
                breedSelect.style.display = 'block';
                breedSelect.required = true;
                manualBreedInput.required = false;
                manualBreedInput.value = '';
                toggleManualBreedBtn.innerHTML = '<i class="fas fa-edit"></i>';
                toggleManualBreedBtn.title = 'Enter custom breed';
            } else {
                // Show manual input, hide dropdown
                manualBreedContainer.style.display = 'block';
                breedSelect.style.display = 'none';
                breedSelect.required = false;
                manualBreedInput.required = true;
                breedSelect.value = '';
                toggleManualBreedBtn.innerHTML = '<i class="fas fa-list"></i>';
                toggleManualBreedBtn.title = 'Select from list';
                manualBreedInput.focus();
            }
        });

        // Handle breed selection change
        breedSelect.addEventListener('change', function () {
            if (this.value === 'Other') {
                // Automatically switch to manual input when "Other" is selected
                toggleManualBreedBtn.click();
            }
        });

        // Check if manual breed was previously entered (for form validation errors)
        if (manualBreedInput.value) {
            toggleManualBreedBtn.click();
        }

        // Run on page load (so it respects old values if the form was reloaded)
        toggleBreedOptions();

        // Run whenever Species changes
        speciesSelect.addEventListener('change', toggleBreedOptions);

        // Set initial values for range sliders
        updateRangeValue('EnergyLevelValue', document.getElementById('EnergyLevel').value);
        updateRangeValue('AppetiteValue', document.getElementById('Appetite').value);
        updateRangeValue('FriendlinessValue', document.getElementById('Friendliness').value);
        updateRangeValue('AdaptabilityValue', document.getElementById('Adaptability').value);
        updateRangeValue('BarkingLevelValue', document.getElementById('BarkingLevel').value);
        updateRangeValue('SheddingLevelValue', document.getElementById('SheddingLevel').value);

        // Add delete image functionality
        document.querySelectorAll('.delete-image').forEach(button => {
            button.addEventListener('click', function () {
                let imageId = this.getAttribute('data-image-id');
                let container = this.closest('.image-container');

                if (confirm('Are you sure you want to delete this image?')) {
                    fetch("{{ route('shelter.pets.deleteimage', '') }}/" + imageId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            container.remove(); // Remove the deleted image from the page

                            // Check if there are no images left
                            const remainingImages = document.querySelectorAll('.image-container');
                            if (remainingImages.length === 0) {
                                document.getElementById('existingImages').innerHTML = `
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> No images uploaded yet.
                                        </div>
                                    </div>
                                `;
                            }
                        } else {
                            alert('Error deleting image: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Network error when trying to delete the image');
                    });
                }
            });
        });

        // Mark initialization as complete
        isDetectionInitialized = true;
    });

    function toggleAllergyDetails(show) {
        const allergyField = document.getElementById('AllergyDetailsContainer');
        if (allergyField) {
            allergyField.style.display = show ? 'block' : 'none';
            if (!show) {
                const allergyInput = document.getElementById('AllergyDetails');
                if (allergyInput) allergyInput.value = '';
            }
        }
    }

    function updateRangeValue(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = value;
        }
    }

    function previewImages(input) {
        const previewContainer = document.getElementById('imagePreviewContainer');
        if (!previewContainer) {
            console.error('Preview container not found');
            return;
        }

        previewContainer.innerHTML = '';

        if (input.files && input.files.length > 0) {
            const filesAmount = input.files.length;

            for (let i = 0; i < filesAmount; i++) {
                const reader = new FileReader();

                reader.onload = function (event) {
                    const previewWrapper = document.createElement('div');
                    previewWrapper.className = 'position-relative';
                    previewWrapper.style.display = 'inline-block';
                    previewWrapper.style.margin = '5px';

                    const img = document.createElement('img');
                    img.setAttribute('src', event.target.result);
                    img.className = 'img-thumbnail';
                    img.style.width = '120px';
                    img.style.height = '120px';
                    img.style.objectFit = 'cover';

                    const badge = document.createElement('span');
                    badge.className = 'badge bg-primary position-absolute';
                    badge.style.top = '5px';
                    badge.style.left = '5px';
                    badge.textContent = `#${i + 1}`;

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'btn btn-sm btn-danger position-absolute';
                    removeBtn.style.top = '0';
                    removeBtn.style.right = '0';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.type = 'button';
                    removeBtn.title = 'Remove preview (image will still upload)';
                    removeBtn.addEventListener('click', function () {
                        previewWrapper.remove();
                    });

                    previewWrapper.appendChild(img);
                    previewWrapper.appendChild(badge);
                    previewWrapper.appendChild(removeBtn);
                    previewContainer.appendChild(previewWrapper);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }
    }

    function showAIStatus(message, isSuccess = false) {
        const statusDiv = document.getElementById('aiDetectionStatus');
        const messageSpan = document.getElementById('detectionMessage');

        if (!statusDiv || !messageSpan) {
            console.error('AI status elements not found');
            return;
        }

        statusDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-info'} d-block`;

        // Format message with line breaks and preserve formatting
        messageSpan.innerHTML = message.replace(/\n/g, '<br>');

        if (isSuccess) {
            setTimeout(() => {
                statusDiv.classList.add('d-none');
            }, 10000); // Hide after 10 seconds for success messages
        }
    }

    function clearAIDetection() {
        const aiDetectedBadge = document.getElementById("aiDetectedBadge");
        const statusDiv = document.getElementById('aiDetectionStatus');

        if (aiDetectedBadge) {
            aiDetectedBadge.classList.add('d-none');
        }

        if (statusDiv) {
            statusDiv.classList.add('d-none');
        }
    }

    function handleImageUpload(input) {
        console.log("=== handleImageUpload called ===");
        console.log("Files:", input.files);

        // Clear previous AI detection results
        clearAIDetection();

        // Preview images
        previewImages(input);

        if (input.files && input.files.length > 0) {
            console.log("Starting AI detection for", input.files.length, "files");

            // Show initial status with appropriate message
            const fileCount = input.files.length;
            const statusMessage = fileCount === 1
                ? '🤖 Analyzing image for breed & color detection...'
                : `🤖 Analyzing ${fileCount} images for breed & color detection...`;

            showAIStatus(statusMessage);

            // Run both breed and color detection
            autoDetectBreed(input.files);

            // Run color detection after a short delay
            setTimeout(() => {
                autoDetectColor(input.files);
            }, 1000);
        } else {
            console.log("No files selected");
            clearAIDetection();
        }
    }

    // Main breed detection function
    function autoDetectBreed(files) {
        console.log("=== autoDetectBreed started ===");
        console.log("Files to process:", files.length);

        const fileCount = files.length;
        const statusMessage = fileCount === 1
            ? 'Analyzing image for breed detection...'
            : `Analyzing ${fileCount} images for breed detection...`;

        showAIStatus(statusMessage);

        let formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append("images[]", files[i]);
            console.log(`Added file ${i + 1}:`, files[i].name, files[i].size, "bytes");
        }

        console.log("Making fetch request to:", "{{ route('pet.detectAI') }}");

        fetch("{{ route('pet.detectAI') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
            .then(response => {
                console.log("=== Breed detection response received ===");
                console.log("Response status:", response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log("=== Raw response text ===");
                console.log("Response length:", text.length);

                try {
                    const data = JSON.parse(text);
                    console.log("=== Parsed JSON response ===");
                    console.log("Parsed data:", data);

                    if (data.success) {
                        console.log("=== Processing successful breed detection ===");
                        processBreedDetectionResults(data, fileCount);
                    } else {
                        console.log("=== Breed detection failed ===");
                        console.log("Error:", data.error || 'Unknown error');
                        showAIStatus(`⚠️ Breed detection failed: ${data.error || 'Unknown error'}. Please fill in details manually.`, false);
                    }
                } catch (parseError) {
                    console.error("=== JSON Parse Error ===");
                    console.error("Parse error:", parseError);
                    showAIStatus(`⚠️ Error processing AI response. Please fill in details manually.`, false);
                }
            })
            .catch(err => {
                console.error('=== Breed detection error ===');
                console.error('Error details:', err);
                showAIStatus(`⚠️ AI detection encountered an error: ${err.message}. Please fill in details manually.`, false);
            });
    }

    function processBreedDetectionResults(data, fileCount) {
        // Get DOM elements with null checks
        const manualBreedContainer = document.getElementById('manualBreedContainer');
        const breedSelect = document.getElementById('Breed');
        const manualBreedInput = document.getElementById('ManualBreed');
        const toggleManualBreedBtn = document.getElementById('toggleManualBreed');
        const speciesSelect = document.getElementById("Species");
        const aiDetectedBadge = document.getElementById("aiDetectedBadge");

        if (!breedSelect || !speciesSelect) {
            console.error("Required DOM elements not found");
            return;
        }

        // Make sure dropdown is visible (not manual input)
        if (manualBreedContainer && manualBreedContainer.style.display !== 'none') {
            console.log("Switching from manual input to dropdown");
            manualBreedContainer.style.display = 'none';
            breedSelect.style.display = 'block';
            breedSelect.required = true;
            if (manualBreedInput) {
                manualBreedInput.required = false;
                manualBreedInput.value = '';
            }
            if (toggleManualBreedBtn) {
                toggleManualBreedBtn.innerHTML = '<i class="fas fa-edit"></i>';
                toggleManualBreedBtn.title = 'Enter custom breed';
            }
        }

        // Handle both single and multiple image responses
        const detectedBreed = data.final_prediction || data.breed;
        const detectedSpecies = data.final_species || data.species;

        console.log("Detected values:", {
            breed: detectedBreed,
            species: detectedSpecies
        });

        // Set species
        if (detectedSpecies) {
            console.log("Setting species to:", detectedSpecies);
            speciesSelect.value = detectedSpecies;
            speciesSelect.dispatchEvent(new Event('change'));
        }

        // Set breed
        if (detectedBreed) {
            console.log("Looking for breed option:", detectedBreed);
            const breedOption = breedSelect.querySelector(`option[value="${detectedBreed}"]`);
            console.log("Breed option found:", !!breedOption);

            if (breedOption) {
                console.log("Setting breed in dropdown:", detectedBreed);
                breedSelect.value = detectedBreed;
            } else {
                console.log("Breed not found in dropdown, using manual input:", detectedBreed);
                if (manualBreedContainer && manualBreedInput && toggleManualBreedBtn) {
                    manualBreedContainer.style.display = 'block';
                    breedSelect.style.display = 'none';
                    breedSelect.required = false;
                    manualBreedInput.required = true;
                    manualBreedInput.value = detectedBreed;
                    toggleManualBreedBtn.innerHTML = '<i class="fas fa-list"></i>';
                    toggleManualBreedBtn.title = 'Select from list';
                }
            }
        }

        // Show AI detected badge
        if (aiDetectedBadge) {
            console.log("Showing AI detected badge");
            aiDetectedBadge.classList.remove('d-none');
        }

        // Show breed detection results with appropriate messaging
        let resultMessage = '🤖 Breed Detection Results:\n\n';
        resultMessage += `🐕 Species & Breed: ${detectedSpecies} - ${detectedBreed}\n`;

        if (fileCount === 1) {
            // Single image results
            if (data.confidence) {
                resultMessage += `📊 Confidence: ${data.confidence}%\n`;
            }
        } else {
            // Multiple images results
            if (data.individual_predictions && data.individual_predictions.length > 1) {
                resultMessage += `\n📸 Breed predictions per image:\n`;
                data.individual_predictions.forEach((pred, index) => {
                    resultMessage += `  Image ${index + 1}: ${pred.prediction} (${pred.confidence}%)\n`;
                });

                if (data.vote_summary && Object.keys(data.vote_summary).length > 0) {
                    resultMessage += `\n📊 Breed vote summary: `;
                    resultMessage += Object.entries(data.vote_summary)
                        .map(([breedName, count]) => `${breedName}: ${count}`)
                        .join(', ');
                    resultMessage += '\n';
                }
            }
        }

        resultMessage += '\n⚠️ Please verify the detected information manually';
        showAIStatus(resultMessage, true);

        console.log("=== Breed detection processing completed ===");
    }

    function autoDetectColor(files) {
        console.log("=== autoDetectColor started ===");
        console.log("Files to process:", files.length);

        const fileCount = files.length;
        const statusMessage = fileCount === 1
            ? 'Analyzing image for color detection...'
            : `Analyzing ${fileCount} images for color detection...`;

        // Update status to show color detection
        showAIStatus(statusMessage);

        let formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append("images[]", files[i]);
            console.log(`Added file ${i + 1} for color detection:`, files[i].name);
        }

        console.log("Making color detection request to:", "{{ route('detect.colors') }}");

        fetch("{{ route('detect.colors') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
            .then(response => {
                console.log("=== Color detection response received ===");
                console.log("Response status:", response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("=== Color detection data ===");
                console.log("Parsed data:", data);

                if (data.success) {
                    console.log("=== Processing successful color detection ===");
                    processColorDetectionResults(data, fileCount);
                } else {
                    console.log("=== Color detection failed ===");
                    console.log("Error:", data.error || 'Unknown error');
                    // Don't show error for color detection as it's supplementary
                }
            })
            .catch(err => {
                console.error('=== Color detection error ===');
                console.error('Error details:', err);
                // Don't show error for color detection as it's supplementary
            });
    }

    function processColorDetectionResults(data, fileCount) {
        const colorSelect = document.getElementById('Color');

        if (!colorSelect) {
            console.error("Color select element not found");
            return;
        }

        // Clear existing selections
        Array.from(colorSelect.options).forEach(option => {
            option.selected = false;
        });

        // Select detected colors
        if (data.colors && data.colors.length > 0) {
            console.log("Setting detected colors:", data.colors);

            data.colors.forEach(color => {
                // Try to find exact match first
                let option = colorSelect.querySelector(`option[value="${color}"]`);

                // If no exact match, try partial matches
                if (!option) {
                    const colorLower = color.toLowerCase();
                    option = Array.from(colorSelect.options).find(opt =>
                        opt.value.toLowerCase().includes(colorLower) ||
                        colorLower.includes(opt.value.toLowerCase())
                    );
                }

                if (option) {
                    option.selected = true;
                    console.log(`Selected color: ${option.value}`);
                } else {
                    console.log(`Color not found in dropdown: ${color}`);
                }
            });

            // Update AI status with color results
            let colorMessage = '🎨 Color Detection Results:\n\n';
            colorMessage += `🌈 Detected Colors: ${data.colors.join(', ')}\n`;

            if (fileCount > 1 && data.individual_results) {
                colorMessage += `\n📸 Colors per image:\n`;
                data.individual_results.forEach((result, index) => {
                    colorMessage += `  Image ${index + 1}: ${result.colors.join(', ')}\n`;
                });
            }

            colorMessage += '\n⚠️ Please verify the detected colors manually';

            // Show color results after a brief delay to not interfere with breed detection
            setTimeout(() => {
                showAIStatus(colorMessage, true);
            }, 2000);
        }
    }

    // Form validation
    document.addEventListener('DOMContentLoaded', function () {
        'use strict';

        // Fetch all forms we want to apply custom validation to
        var forms = document.getElementsByClassName('needs-validation');

        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection