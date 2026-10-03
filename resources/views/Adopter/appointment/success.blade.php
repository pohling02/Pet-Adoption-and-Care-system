@extends('layouts.adopter_master')

@section('title', 'Appointment Successful')

@push('styles')
<style>
    .success-container {
        background: #f9fdfc;
        padding: 40px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 50px auto;
    }
    .success-icon {
        font-size: 50px;
        color: #28a745;
    }
    .btn-home {
        background-color: #007bff;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        padding: 12px;
        transition: 0.3s;
    }
    .btn-home:hover {
        background-color: #0056b3;
    }
</style>
@endpush

@section('content')
<div class="container d-flex justify-content-center">
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h2 class="text-success mt-3 fw-bold">Your appointment has been successfully booked with the following details:</h2>
        <p><strong>Pet Name:</strong> {{ $appointment->pet->PetName }}</p>
    <p><strong>Appointment Date:</strong> {{ \Carbon\Carbon::parse($appointment->AppointmentDate)->format('d M Y h:i A') }}</p>
    <p><strong>Doctor:</strong> {{ $appointment->doctor->DoctorName }}</p>
    <p><strong>Purpose:</strong> {{ $appointment->Purpose }}</p>
    <p><strong>Status:</strong> {{ $appointment->Status }}</p>

    <p>Thank you for using our system. We look forward to seeing you!</p>

    <p>Best regards,</p>
    <p><strong>Pet Adoption System Team</strong></p>
        <p class="text-muted">Our veterinary team will see you soon. Check your email for confirmation.</p>
        <a href="{{ route('adopter.home') }}" class="btn btn-home mt-3">🏡 Return to Home</a>
    </div>
</div>
@endsection
