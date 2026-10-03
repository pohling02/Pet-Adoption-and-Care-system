@extends('layouts.shelter_master')

@section('title', 'Edit Health Record')

@section('content')
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error:</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Validation Errors:</strong>
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 700px;">
        <div class="card-body p-4">
            <h2 class="text-center text-primary fw-bold">
                <i class="fas fa-notes-medical"></i> Edit Pet Health Record
            </h2>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('shelter.health.update', $healthRecord->RecordID) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Pet Name (Disabled) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pet Name</label>
                    <input type="text" class="form-control" value="{{ $healthRecord->pet->PetName }}" disabled>
                </div>

                <!-- Vaccination Status (Dropdown) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Vaccination Status</label>
                    <input type="text" class="form-control" value="{{ $healthRecord->pet->VaccinationStatus }}" disabled>
                </div>

                <!-- Health Remarks -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Health Remarks</label>
                    <textarea name="HealthRemarks" class="form-control @error('HealthRemarks') is-invalid @enderror"
                              rows="3" placeholder="Enter any health-related remarks">{{ $healthRecord->HealthRemarks }}</textarea>
                    @error('HealthRemarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Allergy -->
                <div class="mb-3">
                    <label class="form-label d-block">Allergy</label>
                    <div class="btn-group" role="group" aria-label="Allergy options">
                        <input type="radio" class="btn-check" id="AllergyYes" name="Allergy" value="1" 
                               onclick="toggleAllergyDetails(true)" 
                               {{ $healthRecord->pet->Allergy ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="AllergyYes">Yes</label>
                        <input type="radio" class="btn-check" id="AllergyNo" name="Allergy" value="0" 
                               onclick="toggleAllergyDetails(false)" 
                               {{ !$healthRecord->pet->Allergy ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger" for="AllergyNo">No</label>
                    </div>
                    @error('Allergy')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="AllergyDetailsContainer" style="{{ $healthRecord->pet->Allergy ? 'display:block' : 'display:none' }}">
                    <label for="AllergyDetails" class="form-label">Allergy To What?</label>
                    <input type="text" class="form-control @error('AllergyDetails') is-invalid @enderror" 
                           id="AllergyDetails" name="AllergyDetails" 
                           value="{{ old('AllergyDetails', $healthRecord->pet->AllergyDetails) }}">
                    @error('AllergyDetails')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sterilization (Dropdown) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Sterilization</label>
                    <input type="text" class="form-control" value="{{ $healthRecord->pet->Neutering ? 'Neutered' : 'Not Neutered' }}" disabled>
                </div>

                <!-- Diagnosis -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Diagnosis</label>
                    <textarea name="Diagnosis" class="form-control @error('Diagnosis') is-invalid @enderror"
                              rows="3">{{ $healthRecord->Diagnosis }}</textarea>
                    @error('Diagnosis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Medicine -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Medicine</label>
                    <textarea name="Medicine" class="form-control @error('Medicine') is-invalid @enderror"
                              rows="3">{{ $healthRecord->Medicine }}</textarea>
                    @error('Medicine') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Last Vet Visit -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Last Vet Visit</label>
                    <input type="date" name="LastCheckupDate" class="form-control @error('LastCheckupDate') is-invalid @enderror"
                           value="{{ $healthRecord->LastCheckupDate }}" required>
                    @error('LastCheckupDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Existing Images</label>
                    <div class="d-flex flex-wrap">
                        @foreach($healthRecord->images as $image)
                        <div class="position-relative m-2">
                            <img src="{{ asset('storage/' . $image->ImagePath) }}" width="100px" class="img-thumbnail" alt="Health Record Image">
                            <form action="{{ route('shelter.health.delete-image', $image->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger btn-sm position-absolute" 
                                        style="top: 5px; right: 5px;"
                                        onclick="return confirm('Are you sure you want to delete this image?')">×</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Upload New Images -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Health Record Images</label>
                    <input type="file" class="form-control @error('HealthRecordImages.*') is-invalid @enderror" 
                           name="HealthRecordImages[]" multiple accept="image/*"
                           onchange="previewImages(event)">
                    @error('HealthRecordImages.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="imagePreview" class="mt-2 d-flex flex-wrap"></div>
                    <small class="text-muted">You can upload multiple images (Max 5MB each) and remove them before submitting by clicking the × button.</small>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('shelter.health.view', $healthRecord->pet->PetID) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Image Preview and Handling -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Define the deleteImageUrl variable using a route helper
    const deleteImageUrl = "{{ route('shelter.health.delete-image', ['id' => '__ID__']) }}";
    const deleteButtons = document.querySelectorAll('.delete-image-btn');
    deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
    const imageId = this.getAttribute('data-image-id');
    deleteImage(imageId, this);
    });
    });
    const petData = {
    "{{ $healthRecord->pet->PetID }}": {
    hasAllergy: {{ $healthRecord -> pet -> Allergy ? 'true' : 'false' }},
            allergyDetails: "{{ $healthRecord->pet->AllergyDetails ?? '' }}"
    }
    };
    // Get elements
    const allergyField = document.getElementById('Allergy');
    // Initialize allergy field with proper data if not already set from form data
    // Only do this if the field is empty or unchanged from default
    if (allergyField && (allergyField.value.trim() === '' || allergyField.value === 'No allergies' || allergyField.value === 'Has allergies (details not specified)')) {
    const petId = "{{ $healthRecord->pet->PetID }}";
    if (petData[petId]) {
    // Populate allergy details based on whether the pet has allergies
    if (petData[petId].hasAllergy) {
    // Check if allergyDetails exists and is not empty
    if (petData[petId].allergyDetails && petData[petId].allergyDetails.trim() !== '') {
    allergyField.value = petData[petId].allergyDetails;
    } else {
    allergyField.value = "Has allergies (details not specified)";
    }
    } else {
    allergyField.value = "No allergies";
    }
    }
    }
    });
    // Toggle Allergy Details visibility
    function toggleAllergyDetails(show) {
    document.getElementById('AllergyDetailsContainer').style.display = show ? 'block' : 'none';
    if (!show) {
    document.getElementById('AllergyDetails').value = '';
    }
    }

    // Preview Images Before Uploading with delete functionality
    function previewImages(event) {
    const imagePreview = document.getElementById('imagePreview');
    imagePreview.innerHTML = '';
    // Store the FileList object reference
    const fileInput = event.target;
    const files = Array.from(fileInput.files);
    for (let i = 0; i < files.length; i++) {
    const reader = new FileReader();
    reader.onload = function (e) {
    const imgContainer = document.createElement('div');
    imgContainer.className = 'position-relative me-2 mb-2';
    const img = document.createElement('img');
    img.src = e.target.result;
    img.style.width = '100px';
    img.style.borderRadius = '8px';
    img.style.border = '1px solid #ddd';
    img.style.padding = '5px';
    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
    deleteBtn.innerHTML = '×';
    deleteBtn.style.borderRadius = '50%';
    deleteBtn.style.padding = '0.15rem 0.35rem';
    deleteBtn.style.fontSize = '0.75rem';
    // Add data index to track which file to remove
    deleteBtn.dataset.index = i;
    deleteBtn.onclick = function (e) {
    e.preventDefault();
    const index = parseInt(this.dataset.index);
    removeFile(index, fileInput);
    imgContainer.remove();
    };
    imgContainer.appendChild(img);
    imgContainer.appendChild(deleteBtn);
    imagePreview.appendChild(imgContainer);
    };
    reader.readAsDataURL(files[i]);
    }
    }

    // Function to remove a file from the input
    function removeFile(index, fileInput) {
    const dt = new DataTransfer();
    const files = Array.from(fileInput.files);
    for (let i = 0; i < files.length; i++) {
    if (i !== index) {
    dt.items.add(files[i]);
    }
    }

    fileInput.files = dt.files;
    const deleteButtons = document.querySelectorAll('#imagePreview button[data-index]');
    deleteButtons.forEach((btn, i) => {
    btn.dataset.index = i;
    });
    }

    function deleteImage(imageId, element) {
    if (confirm('Are you sure you want to delete this image?')) {
    // Replace __ID__ with the actual ID
    const url = deleteImageUrl.replace('__ID__', imageId);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(url, {
    method: 'DELETE',
            headers: {
            'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
            },
            credentials: 'same-origin' // This ensures cookies are sent with the request
    })
            .then(response => {
            if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
            })
            .then(data => {
            if (data.success) {
            element.closest('.position-relative').remove();
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('form'));
            } else {
            alert(data.message || 'Failed to delete image');
            }
            })
            .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the image: ' + error.message);
            });
    }
    return false;
    }
</script>
@endsection