@extends('layouts.adopter_master')

@section('title', 'My Appointments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<style>
    .action-btn {
        margin: 3px 5px;
        padding: 6px 15px;
        font-size: 14px;
        border-radius: 5px;
    }
    .btn-cancel {
        background-color: #e0e0e0;
        color: black;
        border: 1px solid #ccc;
    }
    .btn-reschedule {
        background-color: #f0f0f0;
        color: black;
        border: 1px solid #ccc;
    }
    .btn-cancel:hover, .btn-reschedule:hover {
        background-color: #d6d6d6;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3">
            <div class="sidebar shadow p-3 rounded">
                <div class="profile-image mx-auto">
                    @if($profile->profile_picture)
                    <img src="{{ asset($profile->profile_picture) }}" 
                         class="rounded-circle border border-secondary" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                    <div class="profile-initials mx-auto">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    @endif
                    <h5 class="text-center mt-2"><strong>{{ Auth::user()->name }}</strong></h5>
                </div>
                <div class="mt-3">
                    <a href="{{ route('adopter.profile.view') }}" class="btn btn-block">My Profile</a>
                    <a href="{{ route('adopter.pets.profile') }}" class="btn btn-block">Pet Profile</a>
                    <a href="{{ route('adopter.appointments') }}" class="btn btn-block btn-active">My Appointment</a>
                    <a href="{{ route('adopter.adoption') }}" class="btn btn-block">My Adoption</a>
                    <a href="{{ route('adopter.logout') }}" class="btn btn-block btn-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Log Out
                    </a>
                    <form id="logout-form" action="{{ route('adopter.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="col-md-9">
            <div class="card shadow-sm p-4">
                <h4 class="section-title">Appointment List</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Pet Name</th>
                                <th>Date</th>
                                <th>Timeslot</th>
                                <th>Doctor</th>
                                <th>Visit Purpose</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                            @php
                            $appointmentDate = \Carbon\Carbon::parse($appointment->AppointmentDate);
                            $isExpired = $appointmentDate->isPast() && $appointment->Status != 'Cancelled';
                            $isCancelled = $appointment->Status == 'Cancelled';
                            @endphp
                            <tr>
                                <td>{{ $appointment->AppointmentID }}</td>
                                <td>{{ $appointment->pet->PetName ?? 'Unknown' }}</td>
                                <td>{{ $appointmentDate->format('d M Y') }}</td>
                                <td>{{ $appointment->timeslot->Timeslot ?? 'Not Assigned' }}</td>
                                <td>{{ $appointment->doctor->DoctorName ?? 'Unknown' }}</td>
                                <td>{{ $appointment->Purpose }}</td>
                                <td>
                                    <span class="badge 
                                          @if($isExpired) bg-secondary
                                          @elseif($appointment->Status == 'Pending') bg-warning 
                                          @elseif($appointment->Status == 'Confirmed') bg-success 
                                          @elseif($appointment->Status == 'Rejected') bg-danger
                                          @elseif($appointment->Status == 'Emergency Override') bg-danger 
                                          @elseif($appointment->Status == 'Rescheduled') bg-primary 
                                          @elseif($isCancelled) bg-danger
                                          @endif">
                                        {{ $isExpired ? 'Expired' : $appointment->Status }}
                                    </span>
                                </td>
                                <td>
                                    @if(!$isCancelled && !$isExpired)
                                    <div class="d-flex">
                                        @if(!$appointment->is_cancelled)
                                        <button class="btn btn-cancel action-btn" onclick="confirmCancel({{ $appointment->AppointmentID }})">
                                            Cancel
                                        </button>
                                        @endif
                                        @if(!$appointment->is_rescheduled)
                                        <a href="{{ route('appointments.reschedule.form', ['appointment' => $appointment->AppointmentID]) }}" 
                                           class="btn btn-reschedule action-btn">
                                            Reschedule
                                        </a>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No appointment records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this appointment?
            </div>
            <div class="modal-footer">
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-danger">Confirm Cancel</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Cancel Action -->
<script>
    function confirmCancel(appointmentID) {
    let cancelForm = document.getElementById('cancelForm');
    cancelForm.action = "/adopter/appointments/" + appointmentID + "/cancel";
    let cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
    cancelModal.show();
    }
</script>
@endsection
