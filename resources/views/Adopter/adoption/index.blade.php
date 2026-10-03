@extends('layouts.adopter_master')

@section('title', 'My Adoption')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Sidebar (Updated to Match show.blade) -->
        <div class="col-md-3">
            <div class="sidebar shadow p-3 rounded">
                <div class="profile-image mx-auto">
                    @if(isset($profile) && $profile->profile_picture)
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
                    <a href="{{ route('adopter.pets.profile') }}" class="btn btn-block 
                       {{ request()->is('adopter/pets/pet_profile') ? 'btn-active' : '' }}">
                        Pet Profile
                    </a>
                    <a href="{{ route('adopter.appointments') }}" class="btn btn-block">My Appointment</a>
                    <a href="{{ route('adopter.adoption') }}" class="btn btn-block btn-active">My Adoption</a>
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
                <h4 class="section-title">Adoption List</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Pet Name</th>
                                <th>Pet Type</th>
                                <th>Request Status</th>
                                <th>Resubmit Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adoptions as $index => $adoption)
                            <tr>
                                <td>{{ $loop->iteration }}</td> 
                                <td>{{ optional($adoption->pet)->PetName ?? 'Unknown' }}</td>
                                <td>{{ optional($adoption->pet)->Species ?? 'Unknown' }}</td>
                                <td>
                                    <a href="{{ route('adoption.details', $adoption->ApplicationID) }}" 
                                       class="@if($adoption->AdoptionStatus == 'Approved') text-primary 
                                       @elseif($adoption->AdoptionStatus == 'Rejected') text-warning 
                                       @else text-secondary @endif">
                                        {{ ucfirst($adoption->AdoptionStatus) }}
                                    </a>
                                </td>
                                <td>
                                    @if($adoption->AdoptionStatus == 'Rejected' && $adoption->LastRejectionDate)
                                    {{ \Carbon\Carbon::parse($adoption->LastRejectionDate)->addDays(7)->format('Y-m-d') }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    @if($adoption->AdoptionStatus == 'Approved')
                                    <a href="{{ route('adoption.details', $adoption->ApplicationID) }}" class="text-primary">
                                        View Details
                                    </a>
                                    @elseif($adoption->AdoptionStatus == 'Rejected')
                                    <div class="info-box">
                                        <div class="info-item">
                                            <strong>Rejection Reason:</strong> {{ $adoption->StaffNotes }}
                                        </div>
                                    </div>
                                    @elseif(in_array($adoption->AdoptionStatus, ['Pending', 'Under Review']))
                                    <a href="{{ route('adoption.details', $adoption->ApplicationID) }}" class="text-secondary me-2">
                                        View Details
                                    </a>
                                    <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $adoption->ApplicationID }}">
                                        Cancel
                                    </a>

                                    <!-- Cancel Modal -->
                                    <div class="modal fade" id="cancelModal{{ $adoption->ApplicationID }}" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="cancelModalLabel">Cancel Adoption Application</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('adoption.cancel', $adoption->ApplicationID) }}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to cancel your adoption application for <strong>{{ optional($adoption->pet)->PetName ?? 'this pet' }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label for="cancellationReason" class="form-label">Please provide a reason for cancellation:</label>
                                                            <textarea class="form-control" id="cancellationReason" name="cancellationReason" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-secondary">{{ $adoption->AdoptionStatus }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No adoption records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="mt-3">
                    <strong>Total Requests:</strong> {{ count($adoptions) }} | 
                    <strong>Approved:</strong> {{ $approved_count }} | 
                    <strong>Pending:</strong> {{ $pending_count }} | 
                    <strong>Rejected:</strong> {{ $rejected_count }} | 
                    <strong>Under Review:</strong> {{ $under_review_count }} |
                    <strong>Cancelled:</strong> {{ $cancelled_count }}
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
