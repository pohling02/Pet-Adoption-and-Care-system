@extends('layouts.adopter_master')

@section('title', 'Book a Veterinary Appointment')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }
    .appointment-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .input-field {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ced4da;
    }
    .form-label {
        font-weight: 600;
    }
    .btn-primary {
        background-color: #007bff;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        padding: 12px;
        transition: 0.3s;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-book-appointment {
        background-color: #1F4EA4 !important;
        border-color: #1F4EA4 !important;
        color: #fff !important;
    }
    .notice-box {
        background-color: #fff3cd;
        border-left: 5px solid #ffc107;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="appointment-card">
                <h3 class="text-center mb-4 fw-bold">🐾 Book a Veterinary Appointment</h3>

                <!-- Notice Box for Adopted Pets Only -->
                <div class="notice-box">
                    <p class="mb-0"><i class="bi bi-exclamation-triangle-fill"></i> <strong>Important:</strong> Veterinary appointments can only be made for adopted pets. If you haven't adopted a pet yet, please visit our adoption section first.</p>
                </div>

                @if(count($adoptedPets) == 0)
                <div class="alert alert-warning text-center">
                    <i class="bi bi-info-circle me-2"></i>You currently don't have any adopted pets. Please <a href="{{ route('adopter.petlist') }}" class="alert-link">adopt a pet</a> before booking a veterinary appointment.
                                                                                                            

                </div>
                @else
                <form action="{{ route('appointments.store') }}" method="POST">
                    @csrf

                    <!-- Date, Doctor, and Timeslot -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar3"></i> Select Date:</label>
                            <input type="date" id="appointment-date" name="date" class="form-control input-field" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-person-circle"></i> Select Doctor:</label>
                            <select id="doctor-select" name="doctor_id" class="form-select input-field" required>
                                <option value="">Select a Doctor</option>
                                @foreach($doctors as $doctor)
                                <option value="{{ $doctor->DoctorID }}">{{ $doctor->DoctorName }} ({{ $doctor->Specialization }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-clock"></i> Select Timeslot:</label>
                            <select id="timeslot-select" name="timeslot_id" class="form-select input-field" required>
                                <option value="">Select a Timeslot</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-paw"></i> Select Pet:</label>
                            <select id="pet-select" name="pet_id" class="form-select input-field" required>
                                <option value="">Select a Pet</option>
                                @foreach($adoptedPets as $adoption)
                                @if($adoption->pet)
                                <option value="{{ $adoption->pet->PetID }}"
                                        data-dob="{{ $adoption->pet->DateOfBirth }}"
                                        data-gender="{{ $adoption->pet->Gender }}"
                                        data-breed="{{ $adoption->pet->Breed }}">
                                    {{ $adoption->pet->PetName }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            @if($errors->has('pet_id'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('pet_id') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Age, Gender, Breed Inputs -->
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar2"></i> Age:</label>
                            <input type="text" id="pet-age" name="pet_age" class="form-control input-field" placeholder="Age" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-gender-ambiguous"></i> Gender:</label>
                            <select id="pet-gender" name="pet_gender" class="form-select input-field" required readonly>
                                <option value="">Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-tag"></i> Breed:</label>
                            <input type="text" id="pet-breed" name="pet_breed" class="form-control input-field" placeholder="Breed" required readonly>
                        </div>
                    </div>

                    <!-- Visit Purpose -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label"><i class="bi bi-chat-dots"></i> Visit Purpose:</label>
                            <textarea id="purpose" name="purpose" class="form-control input-field" placeholder="Enter the reason for the visit" rows="3" required></textarea>
                        </div>
                    </div>


                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-book-appointment w-100 mt-4">
                        📅 Book Appointment
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Dynamic Fields -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let doctorSelect = document.getElementById("doctor-select");
        let dateSelect = document.getElementById("appointment-date");
        let timeslotSelect = document.getElementById("timeslot-select");
        let petSelect = document.getElementById("pet-select");
        let petAge = document.getElementById("pet-age");
        let petGender = document.getElementById("pet-gender");
        let petBreed = document.getElementById("pet-breed");

        function fetchTimeslots() {
            let doctorId = doctorSelect.value;
            let date = dateSelect.value;

            if (!doctorId || !date) {
                return;
            }

            let url = "{{ route('appointments.get-timeslots') }}?doctor_id=" + doctorId + "&date=" + date;
            console.log("Fetching timeslots from:", url);

            timeslotSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Received timeslots:", data);
                        timeslotSelect.innerHTML = '<option value="">Select a Timeslot</option>';

                        if (data.error) {
                            alert(data.error); // Display the error message in an alert box
                            return;
                        }

                        Object.entries(data).forEach(([key, value]) => {
                            let option = document.createElement("option");
                            option.value = key;
                            option.textContent = value;
                            timeslotSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error("Error fetching timeslots:", error);
                        alert("Failed to load timeslots. Please try again.");
                    });
        }


        function calculateAge(dob) {
            if (!dob)
                return "N/A";
            let birthDate = new Date(dob);
            if (isNaN(birthDate.getTime()))
                return "Invalid Date";

            let today = new Date();
            let years = today.getFullYear() - birthDate.getFullYear();
            let months = today.getMonth() - birthDate.getMonth();
            if (months < 0) {
                years--;
                months += 12;
            }

            return years + " years " + months + " months";
        }

        function autoFillPetDetails() {
            let selectedOption = petSelect.options[petSelect.selectedIndex];

            if (selectedOption.value) {
                let dob = selectedOption.getAttribute("data-dob");
                petAge.value = dob ? calculateAge(dob) : "N/A";
                petGender.value = selectedOption.getAttribute("data-gender") || "N/A";
                petBreed.value = selectedOption.getAttribute("data-breed") || "N/A";
            } else {
                petAge.value = "";
                petGender.value = "";
                petBreed.value = "";
            }
        }

        doctorSelect.addEventListener("change", fetchTimeslots);
        dateSelect.addEventListener("change", fetchTimeslots);
        petSelect.addEventListener("change", autoFillPetDetails);
    });
</script>
@endsection