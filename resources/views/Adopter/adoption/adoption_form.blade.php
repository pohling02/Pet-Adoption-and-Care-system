@extends('layouts.adopter_master')

@section('title', 'Adoption Application')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Logo Section -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid" style="max-width: 70%;">
        </div>

        <!-- Adoption Form -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h3 class="fw-bold">Adoption Application Form</h3>
                <p class="text-muted small">Please fill out all required fields. Fields marked with * are mandatory.</p>

                <!-- Adopter Information -->
                <h5 class="mt-3">Adopter Information</h5>
                <form action="{{ route('adoption.request', $pet->PetID) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="PetID" value="{{ $pet->PetID }}">

                    <div class="mb-3">
                        <label for="FullName" class="form-label small">Full Name*</label>
                        <input type="text" class="form-control" id="FullName" name="FullName" value="{{ Auth::user()->name }}" placeholder="Your full name" readonly>
                        <small class="form-text text-muted">Auto-filled from your account</small>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="Address" class="form-label small">Home Address*</label>
                        <input type="text" class="form-control" id="Address" name="Address" value="{{ old('Address', Auth::user()->address ?? '') }}" placeholder="Enter your full home address">
                        <small class="form-text text-muted">Please provide your complete home address</small>
                        @error('Address')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Postcode, City, State -->
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label for="Postcode" class="form-label small">Postcode*</label>
                            <input type="text" class="form-control" id="Postcode" name="Postcode" value="{{ old('Postcode') }}" placeholder="e.g., 50000">
                            <small id="postcodeError" class="text-danger"></small>
                            @error('Postcode')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="City" class="form-label small">City*</label>
                            <input type="text" class="form-control" id="City" name="City" value="{{ old('City') }}" placeholder="e.g., Kuala Lumpur">
                            @error('City')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="State" class="form-label small">State*</label>
                            <select class="form-control" id="State" name="State">
                                <option value="" disabled {{ old('State') ? '' : 'selected' }}>Select State</option>
                                <option value="Johor" {{ old('State') == 'Johor' ? 'selected' : '' }}>Johor</option>
                                <option value="Kedah" {{ old('State') == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                                <option value="Kelantan" {{ old('State') == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                                <option value="Melaka" {{ old('State') == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                                <option value="Negeri Sembilan" {{ old('State') == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                                <option value="Pahang" {{ old('State') == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                                <option value="Perak" {{ old('State') == 'Perak' ? 'selected' : '' }}>Perak</option>
                                <option value="Perlis" {{ old('State') == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                                <option value="Penang" {{ old('State') == 'Penang' ? 'selected' : '' }}>Penang</option>
                                <option value="Selangor" {{ old('State') == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                                <option value="Terengganu" {{ old('State') == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                            </select>
                            <small class="form-text text-muted">Your state of residence</small>
                            @error('State')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Email (readonly) -->
                    <div class="mb-3 mt-2">
                        <label for="Email" class="form-label small">Email Address*</label>
                        <input type="email" class="form-control" id="Email" name="Email" value="{{ Auth::user()->email }}" placeholder="Your email address" readonly>
                        <small class="form-text text-muted">Auto-filled from your account</small>
                    </div>

                    <!-- Phone No -->
                    <div class="mb-3">
                        <label for="PhoneNo" class="form-label small">Phone Number*</label>
                        <input type="text" class="form-control" id="PhoneNo" name="PhoneNo" value="{{ old('PhoneNo', Auth::user()->phone ?? '') }}" placeholder="e.g., 0123456789">
                        <small class="form-text text-muted">Please provide a valid contact number</small>
                        @error('PhoneNo')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Age and Gender -->
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="Age" class="form-label small">Your Age*</label>
                            <input type="number" class="form-control" id="Age" name="Age" value="{{ old('Age') }}" placeholder="e.g., 25">
                            <small class="form-text text-muted">You must be 18 or older to adopt</small>
                            @error('Age')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="Gender" class="form-label small">Gender*</label>
                            <select class="form-control" id="Gender" name="Gender">
                                <option value="" disabled {{ old('Gender') ? '' : 'selected' }}>Select Gender</option>
                                <option value="Male" {{ old('Gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('Gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('Gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('Gender')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Occupation -->
                    <div class="mb-3 mt-2">
                        <label for="Occupation" class="form-label small">Occupation*</label>
                        <input type="text" class="form-control" id="Occupation" name="Occupation" value="{{ old('Occupation') }}" placeholder="e.g., Software Engineer, Teacher, etc.">
                        <small class="form-text text-muted">Your current job or profession</small>
                        @error('Occupation')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Reason for Adoption -->
                    <div class="mb-3">
                        <label for="ReasonForAdoption" class="form-label small">Reason for Adoption*</label>
                        <textarea class="form-control" id="ReasonForAdoption" name="ReasonForAdoption" rows="2" placeholder="Please explain why you want to adopt this pet" required>{{ old('ReasonForAdoption') }}</textarea>
                        <small class="form-text text-muted">Be specific about why you're interested in this particular pet</small>
                        @error('ReasonForAdoption')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Household Information -->
                    <h5 class="mt-3">Household Information</h5>
                    <div class="mb-3">
                        <label for="HouseholdDetails" class="form-label small">Household Details*</label>
                        <textarea class="form-control" id="HouseholdDetails" name="HouseholdDetails" rows="2" placeholder="e.g., 2-bedroom apartment, fenced yard, landlord allows pets, etc.">{{ old('HouseholdDetails') }}</textarea>
                        <small class="form-text text-muted">Describe your living situation and if you have permission for pets</small>
                        @error('HouseholdDetails')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="OtherPetsInfo" class="form-label small">Other Pets*</label>
                        <textarea class="form-control" id="OtherPetsInfo" name="OtherPetsInfo" rows="2" placeholder="e.g., 1 dog (3 years old), 2 cats (5 years old), etc. or 'No other pets'">{{ old('OtherPetsInfo') }}</textarea>
                        <small class="form-text text-muted">List any existing pets, their species, ages, and temperament</small>
                        @error('OtherPetsInfo')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Pet Care Plan -->
                    <h5 class="mt-3">Pet Care Plan</h5>
                    <div class="mb-3">
                        <label for="PetCarePlan" class="form-label small">Care Plan Details*</label>
                        <textarea class="form-control" id="PetCarePlan" name="PetCarePlan" rows="3" placeholder="Include details about feeding, exercise, vet care, daily routine, etc.">{{ old('PetCarePlan') }}</textarea>
                        <small class="form-text text-muted">Explain how you'll provide for the pet's daily needs and well-being</small>
                        @error('PetCarePlan')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Emergency Plan -->
                    <h5 class="mt-3">Emergency Plan</h5>
                    <div class="mb-3">
                        <label for="EmergencyPlan" class="form-label small">Emergency Care Plan*</label>
                        <textarea class="form-control" id="EmergencyPlan" name="EmergencyPlan" rows="2" placeholder="e.g., savings for vet bills, pet insurance, nearby 24-hour clinic, etc.">{{ old('EmergencyPlan') }}</textarea>
                        <small class="form-text text-muted">How will you handle unexpected medical issues and expenses?</small>
                        @error('EmergencyPlan')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Agreements -->
                    <h5 class="mt-3">Agreements</h5>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" id="SpayNeuterAgreement" name="SpayNeuterAgreement" value="1" {{ old('SpayNeuterAgreement') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="SpayNeuterAgreement">I agree to spay/neuter the pet if not already done.*</label>
                        <small class="d-block text-muted">This helps control pet population and improves pet health</small>
                        @error('SpayNeuterAgreement')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" id="ReturnAgreement" name="ReturnAgreement" value="1" {{ old('ReturnAgreement') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="ReturnAgreement">I agree to return the pet to the shelter if I can no longer care for it.*</label>
                        <small class="d-block text-muted">This ensures the pet will always have proper care</small>
                        @error('ReturnAgreement')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Reference Details -->
                    <h5 class="mt-3">Reference Details</h5>
                    <div class="mb-3">
                        <label for="ReferenceName" class="form-label small">Reference Name*</label>
                        <input type="text" class="form-control" id="ReferenceName" name="ReferenceName" value="{{ old('ReferenceName') }}" placeholder="Full name of your reference">
                        <small class="form-text text-muted">Someone who can vouch for your ability to care for a pet</small>
                        @error('ReferenceName')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="ReferenceContact" class="form-label small">Reference Contact*</label>
                            <input type="text" class="form-control" id="ReferenceContact" name="ReferenceContact" value="{{ old('ReferenceContact') }}" placeholder="Phone number or email">
                            <small class="form-text text-muted">How we can contact your reference</small>
                            @error('ReferenceContact')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="ReferenceRelationship" class="form-label small">Relationship*</label>
                            <input type="text" class="form-control" id="ReferenceRelationship" name="ReferenceRelationship" value="{{ old('ReferenceRelationship') }}" placeholder="e.g., Friend, Family, Colleague">
                            <small class="form-text text-muted">Your relationship to this person</small>
                            @error('ReferenceRelationship')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Living Environment Photos -->
                    <h5 class="mt-3">Living Environment Photos</h5>
                    <div class="mb-3">
                        <label for="photoInput" class="form-label small">Home Photos*</label>
                        <input type="file" class="form-control" id="photoInput" name="LivingEnvironmentPhotos[]" accept="image/*" multiple onchange="previewImages(event)">
                        <small class="form-text text-muted">Upload images of pet living areas (yard, sleeping area, play space, etc.)</small>
                        @error('LivingEnvironmentPhotos.*')
                        <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                        <div id="imagePreview" class="mt-2 d-flex flex-wrap"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 mt-3">Submit Application</button>
                    <small class="d-block text-center text-muted mt-2">By submitting, you confirm all information is accurate and complete</small>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Global DataTransfer to hold files
    const statePostcodeMapping = {
        "Johor": /^7\d{4}$/,
        "Kedah": /^05\d{3}$/, // Example pattern, adjust as needed
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

    let dt = new DataTransfer();

    function previewImages(event) {
        const input = event.target;
        const imagePreview = document.getElementById('imagePreview');

        // Clear our DataTransfer and preview area
        dt = new DataTransfer();
        imagePreview.innerHTML = '';

        // Add each selected file to the DataTransfer object
        for (let i = 0; i < input.files.length; i++) {
            dt.items.add(input.files[i]);
        }
        // Update file input with the new DataTransfer files
        input.files = dt.files;

        // Display preview for each file with a delete button
        for (let i = 0; i < dt.files.length; i++) {
            const file = dt.files[i];
            const reader = new FileReader();
            reader.onload = function (e) {
                // Create a container div for image and delete button
                const container = document.createElement('div');
                container.style.position = 'relative';
                container.style.display = 'inline-block';
                container.style.marginRight = '10px';
                container.style.marginBottom = '10px';

                // Create the image element
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.borderRadius = '8px';
                img.style.border = '1px solid #ddd';
                img.style.padding = '5px';

                // Create the delete button
                const deleteBtn = document.createElement('button');
                deleteBtn.innerHTML = 'X';
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
                deleteBtn.setAttribute('data-index', i);
                deleteBtn.onclick = function () {
                    removeImage(parseInt(deleteBtn.getAttribute('data-index')));
                };

                container.appendChild(img);
                container.appendChild(deleteBtn);
                imagePreview.appendChild(container);
            }
            reader.readAsDataURL(file);
        }
    }

    function removeImage(index) {
        // Get the file input element
        const input = document.getElementById('photoInput');
        // Convert current DataTransfer files to an array
        let filesArray = Array.from(dt.files);
        // Remove the file at the specified index
        filesArray.splice(index, 1);
        // Rebuild DataTransfer with remaining files
        dt = new DataTransfer();
        filesArray.forEach(file => dt.items.add(file));
        // Update the input files and refresh preview
        input.files = dt.files;
        previewImages({target: input});
    }

    function validateStatePostcode() {
        const state = document.querySelector('select[name="State"]').value;
        const postcode = document.querySelector('input[name="Postcode"]').value;
        const pattern = statePostcodeMapping[state];

        if (!pattern)
            return; // No pattern available for the selected state

        const errorMessageElement = document.getElementById('postcodeError');
        if (!pattern.test(postcode)) {
            // Display error if the postcode doesn't match the state pattern
            errorMessageElement.textContent = 'The postcode does not match the selected state.';
        } else {
            // Clear error message if the input is valid
            errorMessageElement.textContent = '';
        }
    }

    // Attach event listeners to both the state and postcode fields
    document.querySelector('select[name="State"]').addEventListener('change', validateStatePostcode);
    document.querySelector('input[name="Postcode"]').addEventListener('input', validateStatePostcode);
</script>

<!-- Custom Styles -->
<style>
    .form-control {
        border-radius: 8px;
        font-size: 16px;
    }
    .card {
        background-color: white;
        border-radius: 10px;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .form-text {
        font-size: 0.75rem;
    }
    /* Add asterisk to required field labels */
    .form-label:after {
        content: "*";
        color: red;
        margin-left: 2px;
    }
</style>
@endsection