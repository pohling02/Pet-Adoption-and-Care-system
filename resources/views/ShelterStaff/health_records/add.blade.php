@extends('layouts.shelter_master')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <h2 class="text-center text-primary fw-bold">
                <i class="fas fa-notes-medical"></i> Add Pet Health Record
            </h2>

            <!-- Success Message -->
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('shelter.health.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Select Pet -->
                <div class="mb-3">
                    <label for="PetID" class="form-label fw-semibold">Select Pet</label>
                    <select name="PetID" id="PetID" class="form-select @error('PetID') is-invalid @enderror">
                        <option value="" disabled selected>Select a pet</option>
                        @foreach ($pets as $pet)
                        <option value="{{ $pet->PetID }}" {{ old('PetID') == $pet->PetID ? 'selected' : '' }}>
                            {{ $pet->PetName }}
                        </option>
                        @endforeach
                    </select>
                    @error('PetID')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Vaccination Status (Read-only) -->
                <div class="mb-3">
                    <label for="VaccinationStatus" class="form-label fw-semibold">Vaccination Status</label>
                    <input type="text" id="VaccinationStatus" class="form-control" disabled>
                    <input type="hidden" name="VaccinationStatus" id="VaccinationStatusHidden">
                </div>

                <!-- Sterilization (Read-only) -->
                <div class="mb-3">
                    <label for="Sterilization" class="form-label fw-semibold">Sterilization</label>
                    <input type="text" id="Sterilization" class="form-control" disabled>
                    <input type="hidden" name="Sterilization" id="SterilizationHidden">
                </div>

                <!-- Allergy -->
                <div class="mb-3">
                    <label class="form-label d-block">Allergy</label>
                    <div class="btn-group" role="group" aria-label="Allergy options">
                        <input type="radio" class="btn-check" id="AllergyYes" name="Allergy" value="1" onclick="toggleAllergyDetails(true)">
                        <label class="btn btn-outline-success" for="AllergyYes">Yes</label>
                        <input type="radio" class="btn-check" id="AllergyNo" name="Allergy" value="0" onclick="toggleAllergyDetails(false)">
                        <label class="btn btn-outline-danger" for="AllergyNo">No</label>
                    </div>
                    @error('Allergy')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="AllergyDetailsContainer" style="display:none">
                    <label for="AllergyDetails" class="form-label">Allergy To What?</label>
                    <input type="text" class="form-control @error('AllergyDetails') is-invalid @enderror" id="AllergyDetails" name="AllergyDetails">
                    @error('AllergyDetails')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Health Remarks -->
                <div class="mb-3">
                    <label for="HealthRemarks" class="form-label fw-semibold">Health Remarks</label>
                    <textarea name="HealthRemarks" id="HealthRemarks" 
                              class="form-control @error('HealthRemarks') is-invalid @enderror" 
                              rows="3" placeholder="Enter any health-related remarks">{{ old('HealthRemarks') }}</textarea>
                    @error('HealthRemarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Diagnosis -->
                <div class="mb-3">
                    <label for="Diagnosis" class="form-label fw-semibold">Diagnosis</label>
                    <textarea name="Diagnosis" id="Diagnosis" 
                              class="form-control @error('Diagnosis') is-invalid @enderror"
                              rows="3" placeholder="Enter diagnosis details">{{ old('Diagnosis') }}</textarea>
                    @error('Diagnosis')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Medicine -->
                <div class="mb-3">
                    <label for="Medicine" class="form-label fw-semibold">Medicine</label>
                    <textarea name="Medicine" id="Medicine" 
                              class="form-control @error('Medicine') is-invalid @enderror"
                              rows="3" placeholder="Enter prescribed medicines">{{ old('Medicine') }}</textarea>
                    @error('Medicine')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Upload Health Record Images -->
                <div class="mb-3">
                    <label for="HealthRecordImages" class="form-label fw-semibold">Upload Health Record Images (multiple allowed)</label>
                    <input type="file" name="HealthRecordImages[]" id="HealthRecordImages" 
                           class="form-control @error('HealthRecordImages.*') is-invalid @enderror" 
                           accept="image/*" multiple onchange="previewImages(event)">
                    @error('HealthRecordImages.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="imagePreview" class="mt-2 d-flex flex-wrap"></div>
                    <small class="text-muted">Click the × button on any image to remove it before submitting</small>
                </div>

                <!-- Last Checkup Date -->
                <div class="mb-3">
                    <label for="LastCheckupDate" class="form-label fw-semibold">Checkup Date</label>
                    <input type="date" name="LastCheckupDate" id="LastCheckupDate" 
                           class="form-control @error('LastCheckupDate') is-invalid @enderror" 
                           value="{{ old('LastCheckupDate') }}" required>
                    @error('LastCheckupDate')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function () {
        // Pet data for auto-filling fields
        const petData = {
            @foreach($pets as $pet)
                "{{ $pet->PetID }}": {
                    vaccinationStatus: "{{ $pet->VaccinationStatus }}",
                    sterilization: "{{ $pet->Neutering ? 'Neutered' : 'Not Neutered' }}",
                    hasAllergy: {{ $pet->Allergy ? 'true' : 'false' }},
                    allergyDetails: "{{ $pet->AllergyDetails ?? '' }}"
                },
            @endforeach
        };

        // Get elements
        const petSelect = document.getElementById('PetID');
        const vaccinationStatusField = document.getElementById('VaccinationStatus');
        const vaccinationStatusHidden = document.getElementById('VaccinationStatusHidden');
        const sterilizationField = document.getElementById('Sterilization');
        const sterilizationHidden = document.getElementById('SterilizationHidden');
        const allergyYes = document.getElementById('AllergyYes');
        const allergyNo = document.getElementById('AllergyNo');
        const allergyDetails = document.getElementById('AllergyDetails');
        const allergyDetailsContainer = document.getElementById('AllergyDetailsContainer');

        if (petSelect) {
            petSelect.addEventListener('change', function () {
                const petId = this.value;
                if (petData[petId]) {
                    // Populate vaccination status
                    vaccinationStatusField.value = petData[petId].vaccinationStatus;
                    vaccinationStatusHidden.value = petData[petId].vaccinationStatus;

                    // Populate sterilization status
                    sterilizationField.value = petData[petId].sterilization;
                    sterilizationHidden.value = petData[petId].sterilization;

                    // Set allergy radio button based on pet data
                    if (petData[petId].hasAllergy) {
                        allergyYes.checked = true;
                        allergyDetailsContainer.style.display = 'block';
                        allergyDetails.value = petData[petId].allergyDetails;
                    } else {
                        allergyNo.checked = true;
                        allergyDetailsContainer.style.display = 'none';
                        allergyDetails.value = '';
                    }
                } else {
                    // Reset fields if no pet is selected
                    vaccinationStatusField.value = "";
                    vaccinationStatusHidden.value = "";
                    sterilizationField.value = "";
                    sterilizationHidden.value = "";
                    allergyNo.checked = true;
                    allergyDetailsContainer.style.display = 'none';
                    allergyDetails.value = '';
                }
            });

            // Trigger change event if a pet is already selected (e.g., from old input)
            if (petSelect.value) {
                petSelect.dispatchEvent(new Event('change'));
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
    // Create a new DataTransfer object
    const dt = new DataTransfer();
    // Convert FileList to array for manipulation
    const files = Array.from(fileInput.files);
    // Add all files except the one to be removed
    for (let i = 0; i < files.length; i++) {
    if (i !== index) {
    dt.items.add(files[i]);
    }
    }

    // Set the new FileList to the input
    fileInput.files = dt.files;
    // Update all delete buttons' data-index attributes
    const deleteButtons = document.querySelectorAll('#imagePreview button[data-index]');
    deleteButtons.forEach((btn, i) => {
    btn.dataset.index = i;
    });
    }
</script>
@endsection