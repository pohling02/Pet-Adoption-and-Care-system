@extends('layouts.shelter_master')

@section('title', 'Reschedule Appointment for Shelter Pet')

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
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="appointment-card">
                <h3 class="text-center mb-4 fw-bold">🐾 Reschedule Appointment for a Shelter Pet</h3>
                <form action="{{ route('shelter.appointment.reschedule', ['appointment' => $appointment->AppointmentID]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Date, Doctor, and Timeslot -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-calendar3"></i> Select New Date:
                            </label>
                            <input type="date" id="appointment-date" name="date" 
                                   class="form-control input-field"
                                   value="{{ $appointment->AppointmentDate }}"
                                   required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-person-circle"></i> Select Doctor:
                            </label>
                            <select id="doctor-select" name="doctor_id" class="form-select input-field" required>
                                @foreach($doctors as $doctor)
                                <option value="{{ $doctor->DoctorID }}"
                                        {{ $appointment->DoctorID == $doctor->DoctorID ? 'selected' : '' }}>
                                    {{ $doctor->DoctorName }} ({{ $doctor->Specialization }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-clock"></i> Select Timeslot:
                            </label>
                            <select id="timeslot-select" name="timeslot_id" class="form-select input-field" required>
                                <option value="">Select a Timeslot</option>
                                @foreach($timeslots as $id => $time)
                                <option value="{{ $id }}" 
                                        {{ $appointment->timeslot_id == $id ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Pet Selection (Read-Only) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-paw"></i> Selected Pet:
                            </label>
                            <input type="text" class="form-control input-field" 
                                   value="{{ $appointment->pet->PetName }}" readonly>
                            <input type="hidden" name="pet_id" value="{{ $appointment->PetID }}">
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

                    <!-- Visit Purpose -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="bi bi-chat-dots"></i> Visit Purpose:
                            </label>
                            <textarea id="purpose" name="purpose" 
                                      class="form-control input-field" 
                                      rows="3" 
                                      required>{{ $appointment->Purpose }}</textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        🔄 Reschedule Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Dynamic Timeslot Fetching -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let doctorSelect = document.getElementById("doctor-select");
        let dateSelect = document.getElementById("appointment-date");
        let timeslotSelect = document.getElementById("timeslot-select");
        let selectedTimeslot = "{{ $appointment->timeslot_id }}"; // Store the previous timeslot

        function fetchTimeslots() {
            let doctorId = doctorSelect.value;
            let date = dateSelect.value;

            if (!doctorId || !date) {
                console.warn("Doctor ID or Date is missing.");
                return;
            }

            let url = "{{ route('shelter.appointments.get-timeslots') }}?doctor_id=" + doctorId + "&date=" + date;
            console.log("Fetching timeslots from:", url);

            timeslotSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch timeslots');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Received timeslots:", data);
                        timeslotSelect.innerHTML = '<option value="">Select a Timeslot</option>';

                        if (data.error) {
                            alert("No available timeslots for the selected date.");
                            return;
                        }

                        let hasPreSelected = false;
                        Object.entries(data).forEach(([key, value]) => {
                            let option = document.createElement("option");
                            option.value = key;
                            option.textContent = value;

                            // Re-select previous timeslot if still available
                            if (key == selectedTimeslot) {
                                option.selected = true;
                                hasPreSelected = true;
                            }
                            timeslotSelect.appendChild(option);
                        });

                        // If the old timeslot isn't available, default to the first available
                        if (!hasPreSelected && timeslotSelect.options.length > 1) {
                            timeslotSelect.options[1].selected = true;
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching timeslots:", error);
                        alert("Failed to load timeslots. Please try again.");
                    });
        }

        doctorSelect.addEventListener("change", fetchTimeslots);
        dateSelect.addEventListener("change", fetchTimeslots);
    });
</script>
@endsection
