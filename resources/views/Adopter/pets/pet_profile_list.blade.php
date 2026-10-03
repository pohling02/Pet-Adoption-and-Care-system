@extends('layouts.adopter_master')

@section('title', 'My Pets')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
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
                    <a href="{{ route('adopter.pets.profile') }}" class="btn btn-block btn-active">Pet Profile</a>
                    <a href="{{ route('adopter.appointments') }}" class="btn btn-block">My Appointment</a>
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
        <div class="col-md-9 pet-list-container">
            <div class="card shadow-sm p-4">
                <h4 class="section-title">Pet List</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Pet Name</th>
                                <th>Pet Type</th>
                                <th>Age</th>
                                <th>Health Condition</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pets as $pet)
                            <tr>
                                <td>{{ $pet->PetCode }}</td>
                                <td>{{ $pet->PetName }}</td>
                                <td>{{ $pet->Species }}</td>
                                <td>{{ \Carbon\Carbon::parse($pet->DateOfBirth)->age }} Years</td>
                                <td>{{ $pet->HealthCondition ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('pet.details', $pet->PetID) }}" class="btn btn-sm btn-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @if($pets->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">No pets found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
