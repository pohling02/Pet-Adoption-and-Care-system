@extends('layouts.adopter_master')

@section('title', 'Resubmit Adoption Application')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold mb-4">Resubmit Adoption Application</h3>
        
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i> Please review and update the information below. Fields marked with <span class="text-danger">*</span> are required. Pay special attention to any fields that were mentioned in the rejection feedback.
        </div>
        
        <form action="{{ route('adoption.resubmit.submit', $adoption->ApplicationID) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- Adopter Information -->
            <h5 class="mb-3">Adopter Information</h5>
            <div class="mb-3">
                <label for="FullName" class="form-label">Full Name</label>
                <input type="text" id="FullName" class="form-control" name="FullName" 
                       value="{{ old('FullName', $adoption->FullName) }}" readonly>
                <small class="text-muted">This field cannot be changed</small>
            </div>
            <div class="mb-3">
                <label for="Address" class="form-label">Address <span class="text-danger">*</span></label>
                <input type="text" id="Address" class="form-control @error('Address') is-invalid @enderror" name="Address" 
                       value="{{ old('Address', $adoption->Address) }}" placeholder="Enter your complete home address">
                @error('Address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <label for="postcodeInput" class="form-label">Postcode <span class="text-danger">*</span></label>
                    <input type="text" id="postcodeInput" class="form-control @error('Postcode') is-invalid @enderror" name="Postcode" 
                           value="{{ old('Postcode', $adoption->Postcode) }}" placeholder="e.g., 50000">
                    <small class="text-muted">Must match the selected state</small>
                    @error('Postcode')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="City" class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" id="City" class="form-control @error('City') is-invalid @enderror" name="City" 
                           value="{{ old('City', $adoption->City) }}" placeholder="e.g., Kuala Lumpur">
                    @error('City')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="stateSelect" class="form-label">State <span class="text-danger">*</span></label>
                    <select class="form-select @error('State') is-invalid @enderror" name="State" id="stateSelect">
                        <option value="" disabled {{ old('State') ? '' : 'selected' }}>Choose State</option>
                        <option value="Johor" {{ old('State', $adoption->State) == 'Johor' ? 'selected' : '' }}>Johor</option>
                        <option value="Kedah" {{ old('State', $adoption->State) == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                        <option value="Kelantan" {{ old('State', $adoption->State) == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                        <option value="Melaka" {{ old('State', $adoption->State) == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                        <option value="Negeri Sembilan" {{ old('State', $adoption->State) == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                        <option value="Pahang" {{ old('State', $adoption->State) == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                        <option value="Perak" {{ old('State', $adoption->State) == 'Perak' ? 'selected' : '' }}>Perak</option>
                        <option value="Perlis" {{ old('State', $adoption->State) == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                        <option value="Penang" {{ old('State', $adoption->State) == 'Penang' ? 'selected' : '' }}>Penang</option>
                        <option value="Selangor" {{ old('State', $adoption->State) == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                        <option value="Terengganu" {{ old('State', $adoption->State) == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                    </select>
                    @error('State')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Postcode-State validation error -->
            <div id="postcodeError" class="mt-2 text-danger" role="alert" aria-live="assertive"></div>

            <div class="mb-3 mt-3">
                <label for="Email" class="form-label">Email</label>
                <input type="email" id="Email" class="form-control" name="Email" 
                       value="{{ old('Email', $adoption->Email) }}" placeholder="Email" readonly>
                <small class="text-muted">This field cannot be changed</small>
            </div>
            <div class="mb-3">
                <label for="PhoneNo" class="form-label">Phone No <span class="text-danger">*</span></label>
                <input type="text" id="PhoneNo" class="form-control @error('PhoneNo') is-invalid @enderror" name="PhoneNo" 
                       value="{{ old('PhoneNo', $adoption->ContactNumber) }}" placeholder="e.g., 0123456789">
                <small class="text-muted">Please enter a valid Malaysian phone number</small>
                @error('PhoneNo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label for="Age" class="form-label">Age <span class="text-danger">*</span></label>
                    <input type="number" id="Age" class="form-control @error('Age') is-invalid @enderror" name="Age" 
                           value="{{ old('Age', $adoption->Age) }}" placeholder="Your current age">
                    <small class="text-muted">You must be at least 18 years old to adopt</small>
                    @error('Age')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="Gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select id="Gender" class="form-select @error('Gender') is-invalid @enderror" name="Gender">
                        <option value="" disabled {{ old('Gender', $adoption->Gender) ? '' : 'selected' }}>Choose Gender</option>
                        <option value="Male" {{ old('Gender', $adoption->Gender)=='Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('Gender', $adoption->Gender)=='Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('Gender', $adoption->Gender)=='Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('Gender')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label for="Occupation" class="form-label">Occupation <span class="text-danger">*</span></label>
                <input type="text" id="Occupation" class="form-control @error('Occupation') is-invalid @enderror" name="Occupation" 
                       value="{{ old('Occupation', $adoption->Occupation) }}" placeholder="Your current job/occupation">
                <small class="text-muted">This helps us assess your lifestyle and capacity to care for a pet</small>
                @error('Occupation')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Application Specific Information -->
            <h5 class="mt-4 mb-3">Pet Adoption Information <span class="text-danger">*</span></h5>
            <div class="mb-3">
                <label for="ReasonForAdoption" class="form-label">Why do you want to adopt? <span class="text-danger">*</span></label>
                <textarea id="ReasonForAdoption" name="ReasonForAdoption" class="form-control @error('ReasonForAdoption') is-invalid @enderror" rows="3" required>{{ old('ReasonForAdoption', $adoption->ReasonForAdoption) }}</textarea>
                <small class="text-muted">Please provide detailed and sincere reasons for wanting to adopt this pet</small>
                @error('ReasonForAdoption')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="PetCarePlan" class="form-label">How will you take care of the pet? <span class="text-danger">*</span></label>
                <textarea id="PetCarePlan" name="PetCarePlan" class="form-control @error('PetCarePlan') is-invalid @enderror" rows="3" required>{{ old('PetCarePlan', $adoption->PetCarePlan) }}</textarea>
                <small class="text-muted">Include details about feeding, exercise, training, veterinary care, and daily routine</small>
                @error('PetCarePlan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Household Information -->
            <h5 class="mt-4 mb-3">Household Information <span class="text-danger">*</span></h5>
            <div class="mb-3">
                <label for="HouseholdDetails" class="form-label">Describe your household <span class="text-danger">*</span></label>
                <textarea id="HouseholdDetails" class="form-control @error('HouseholdDetails') is-invalid @enderror" name="HouseholdDetails" rows="2" placeholder="House type, pet permissions, allergies, etc.">{{ old('HouseholdDetails', $adoption->HouseholdDetails) }}</textarea>
                <small class="text-muted">Include details on: type of residence, rental status, yard space, family members, and if pets are allowed</small>
                @error('HouseholdDetails')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="OtherPetsInfo" class="form-label">Other Pets (if any)</label>
                <textarea id="OtherPetsInfo" class="form-control @error('OtherPetsInfo') is-invalid @enderror" name="OtherPetsInfo" rows="2" placeholder="Please specify types, ages, and temperaments of current pets">{{ old('OtherPetsInfo', $adoption->OtherPetsInfo) }}</textarea>
                <small class="text-muted">If you have other pets, describe their species, age, and how they interact with new animals</small>
                @error('OtherPetsInfo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Emergency Plan -->
            <div class="mb-3">
                <label for="EmergencyPlan" class="form-label">How will you handle medical emergencies for the pet? <span class="text-danger">*</span></label>
                <textarea id="EmergencyPlan" class="form-control @error('EmergencyPlan') is-invalid @enderror" name="EmergencyPlan" rows="2" placeholder="Describe your emergency plan">{{ old('EmergencyPlan', $adoption->EmergencyPlan) }}</textarea>
                <small class="text-muted">Include your preferred vet clinic, financial preparedness for emergencies, and backup caretakers</small>
                @error('EmergencyPlan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Agreements -->
            <h5 class="mt-4 mb-3">Agreements <span class="text-danger">*</span></h5>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" id="SpayNeuterAgreement" name="SpayNeuterAgreement" value="1" 
                       {{ old('SpayNeuterAgreement', $adoption->SpayNeuterAgreement) == 1 ? 'checked' : '' }} required>
                <label class="form-check-label" for="SpayNeuterAgreement">
                    I agree to spay/neuter the pet if not already done.
                </label>
                @error('SpayNeuterAgreement')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" id="ReturnAgreement" name="ReturnAgreement" value="1" 
                       {{ old('ReturnAgreement', $adoption->ReturnAgreement) == 1 ? 'checked' : '' }} required>
                <label class="form-check-label" for="ReturnAgreement">
                    I agree to return the pet if I can no longer care for it.
                </label>
                @error('ReturnAgreement')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reference Details -->
            <h5 class="mt-4 mb-3">Reference Details <span class="text-danger">*</span></h5>
            <div class="mb-3">
                <label for="ReferenceName" class="form-label">Reference Name <span class="text-danger">*</span></label>
                <input type="text" id="ReferenceName" class="form-control @error('ReferenceName') is-invalid @enderror" name="ReferenceName" 
                       value="{{ old('ReferenceName', $adoption->ReferenceName) }}" placeholder="Full name of your reference">
                <small class="text-muted">Please provide a reference who knows you well and can vouch for your ability to care for a pet</small>
                @error('ReferenceName')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label for="ReferenceContact" class="form-label">Reference Contact <span class="text-danger">*</span></label>
                    <input type="text" id="ReferenceContact" class="form-control @error('ReferenceContact') is-invalid @enderror" name="ReferenceContact" 
                           value="{{ old('ReferenceContact', $adoption->ReferenceContact) }}" placeholder="Phone number of your reference">
                    <small class="text-muted">We may contact this person to verify your information</small>
                    @error('ReferenceContact')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="ReferenceRelationship" class="form-label">Relationship to You <span class="text-danger">*</span></label>
                    <input type="text" id="ReferenceRelationship" class="form-control @error('ReferenceRelationship') is-invalid @enderror" name="ReferenceRelationship" 
                           value="{{ old('ReferenceRelationship', $adoption->ReferenceRelationship) }}" placeholder="e.g., Family friend, Colleague">
                    <small class="text-muted">How does this person know you?</small>
                    @error('ReferenceRelationship')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Re-upload Living Environment Photos -->
            <h5 class="mt-4 mb-3">Living Environment Photos</h5>
            <div class="mb-3">
                <label for="photoInput" class="form-label">Upload Images</label>
                <input type="file" class="form-control" id="photoInput" 
                       name="LivingEnvironmentPhotos[]" accept="image/*" multiple 
                       onchange="previewImages(event)" aria-describedby="photoHelp">
                <div id="photoHelp" class="form-text">
                    <ul class="ps-3 mb-0 mt-2">
                        <li>Upload clear photos of your living space where the pet will stay</li>
                        <li>Include images of outdoor areas if applicable</li>
                        <li>Show any pet-specific setups you have (e.g., pet beds, toys, food area)</li>
                        <li>Maximum 5 images, each less than 5MB</li>
                    </ul>
                </div>
                @error('LivingEnvironmentPhotos.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div id="imagePreview" class="mt-2 d-flex flex-wrap"></div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Resubmit Application</button>
                <a href="{{ route('adopter.adoption') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Postcode/State Validation & Image Preview Logic --}}
<script>
    // State-to-postcode patterns (adjust as needed for actual Malaysian postcodes)
    const statePostcodeMapping = {
        "Johor": /^7\d{4}$/,
        "Kedah": /^05\d{3}$/,
        "Kelantan": /^15\d{3}$/,
        "Melaka": /^75\d{3}$/,
        "Negeri Sembilan": /^71\d{3}$/,
        "Pahang": /^25\d{3}$/,
        "Perak": /^30\d{3}$/,
        "Perlis": /^02\d{3}$/,
        "Penang": /^(10\d{3}|11\d{3}|12\d{3}|13\d{3}|14\d{3})$/,
        "Selangor": /^40\d{3}$/,
        "Terengganu": /^21\d{3}$/
    };

    // Validate if the postcode matches the selected state's pattern
    function validateStatePostcode() {
        const state = document.getElementById('stateSelect').value;
        const postcode = document.getElementById('postcodeInput').value.trim();
        const errorElement = document.getElementById('postcodeError');

        if (!statePostcodeMapping[state]) {
            errorElement.textContent = '';
            return;
        }

        if (!statePostcodeMapping[state].test(postcode)) {
            errorElement.textContent = 'The postcode does not match the selected state.';
        } else {
            errorElement.textContent = '';
        }
    }

    // Attach event listeners for real-time validation
    document.getElementById('stateSelect').addEventListener('change', validateStatePostcode);
    document.getElementById('postcodeInput').addEventListener('input', validateStatePostcode);

    // Image Preview with Remove Option
    let dt = new DataTransfer();
    function previewImages(event) {
        const input = event.target;
        const previewContainer = document.getElementById('imagePreview');

        // Reset DataTransfer and preview container
        dt = new DataTransfer();
        previewContainer.innerHTML = '';

        // Add each file to DataTransfer and update input files
        Array.from(input.files).forEach(file => dt.items.add(file));
        input.files = dt.files;

        // Create image preview for each file with a delete button
        Array.from(dt.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.createElement('div');
                container.style.position = 'relative';
                container.style.display = 'inline-block';
                container.style.marginRight = '10px';
                container.style.marginBottom = '10px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.borderRadius = '8px';
                img.style.border = '1px solid #ddd';
                img.style.padding = '5px';

                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'X';
                deleteBtn.setAttribute('aria-label', 'Remove image');
                deleteBtn.style.position = 'absolute';
                deleteBtn.style.top = '0';
                deleteBtn.style.right = '0';
                deleteBtn.style.background = 'red';
                deleteBtn.style.color = 'white';
                deleteBtn.style.border = 'none';
                deleteBtn.style.borderRadius = '50%';
                deleteBtn.style.width = '20px';
                deleteBtn.style.height = '20px';
                deleteBtn.style.cursor = 'pointer';
                deleteBtn.dataset.index = index;
                deleteBtn.onclick = function () {
                    removeImage(parseInt(deleteBtn.dataset.index));
                };

                container.appendChild(img);
                container.appendChild(deleteBtn);
                previewContainer.appendChild(container);
            }
            reader.readAsDataURL(file);
        });
    }

    function removeImage(index) {
        const input = document.getElementById('photoInput');
        let filesArray = Array.from(dt.files);
        filesArray.splice(index, 1);
        dt = new DataTransfer();
        filesArray.forEach(file => dt.items.add(file));
        input.files = dt.files;
        previewImages({target: input});
    }
</script>

<!-- Custom Styles -->
<style>
    .form-control, .form-select {
        border-radius: 8px;
        font-size: 16px;
    }
    .card {
        background-color: #fff;
        border-radius: 10px;
    }
    .text-danger {
        color: #dc3545;
    }
    .small, .text-muted {
        font-size: 0.875em;
    }
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
</style>
@endsection