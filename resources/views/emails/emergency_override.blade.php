@component('mail::message')
# Emergency Override Notification

Hello {{ $appointment->adopter->name }},

This is to notify you that an **Emergency Override** has been executed for your appointment with ID: **{{ $appointment->AppointmentID }}**.

@if(isset($appointment->override_reason))
**Reason for Override:** {{ $appointment->override_reason }}
@else
**Reason for Override:** Due to a shortage of available veterinary doctors or other emergency circumstances.
@endif

Date: {{ \Carbon\Carbon::parse($appointment->AppointmentDate)->format('d M Y h:i A') }}

Purpose: {{ $appointment->Purpose }}

@component('mail::button', ['url' => ''])
View Appointment
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
