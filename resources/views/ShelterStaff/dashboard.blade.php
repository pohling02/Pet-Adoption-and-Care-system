@extends('layouts.shelter_master')

@section('title', 'Shelter Dashboard')

@section('content')
<div class="dashboard-header py-3">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Welcome to Petopia</h2>
        <p class="text-muted mb-0">{{ now()->format('l, F d, Y') }}</p>
    </div>
</div>
<div class="container mt-4">
    <p class="text-muted mb-4">Here's your dashboard for today.</p>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <!-- Manage Pets -->
        <div class="col">
            <div class="card h-100 shadow-sm dashboard-card pets-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Manage Pets</h5>
                            <p class="card-text">You have <strong>{{ $petCount ?? 0 }}</strong> pets in your shelter</p>
                            <div class="pet-stats d-flex gap-3 text-muted mt-3">
                                <div><i class="bi bi-circle-fill text-success"></i> {{ $availablePets ?? 0 }} Available</div>
                            </div>
                        </div>
                        <div class="card-icon-container">
                            <i class="bi bi-house-heart card-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ route('shelter.pets.manage') }}" class="btn btn-outline-primary">View All Pets</a>
                </div>
            </div>
        </div>

        <!-- Adoption Requests -->
        <div class="col">
            <div class="card h-100 shadow-sm dashboard-card adoptions-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Adoption Requests</h5>
                            <p class="card-text"><strong>{{ $pendingAdoptions ?? 0 }}</strong> pending adoption requests</p>
                            @if(($newAdoptionsToday ?? 0) > 0)
                            <div class="notification-badge mt-2">
                                <span class="badge bg-danger">{{ $newAdoptionsToday ?? 0 }} new today</span>
                            </div>
                            @endif
                            @if(($recentCancelledAdoptions ?? 0) > 0)
                            <div class="cancellation-info mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-exclamation-circle text-warning"></i> 
                                    Some adoption requests have been cancelled by adopters
                                </small>
                                <span class="badge bg-warning text-dark">{{ $recentCancelledAdoptions ?? 0 }} cancelled recently</span>
                            </div>
                            @endif
                        </div>
                        <div class="card-icon-container">
                            <i class="bi bi-heart card-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ route('shelter.adoptions') }}" class="btn btn-primary">Review Requests</a>
                </div>
            </div>
        </div>

        <!-- Pet Health Records -->
        <div class="col">
            <div class="card h-100 shadow-sm dashboard-card health-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Pet Health</h5>
                            <p class="card-text">
                                <strong>{{ $petHealthRecords->count() ?? 0 }}</strong> health records available
                            </p>
                            @if($petNeedingAttention > 0)
                            <div class="health-alert mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-exclamation-triangle text-danger"></i> 
                                    {{ $petNeedingAttention }} pet(s) need medical attention!
                                </small>
                            </div>
                            @endif
                        </div>
                        <div class="card-icon-container">
                            <i class="bi bi-clipboard-heart card-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- List of Recent Pet Health Records -->
                <ul class="list-group list-group-flush">
                    @forelse($petHealthRecords as $record)
                    <li class="list-group-item">
                        <small class="text-muted">
                            <strong>{{ $record->PetName }}</strong> - {{ $record->HealthRemarks }} 
                            (Last vet visit: {{ \Carbon\Carbon::parse($record->LastVetVisit)->format('M d, Y') }})
                        </small>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">No health records available.</li>
                    @endforelse
                </ul>

                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ route('shelter.health') }}" class="btn btn-primary">View All Records</a>
                </div>
            </div>
        </div>

        <!-- Message Center -->
        <div class="col">
            <div class="card h-100 shadow-sm dashboard-card messages-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Message Center</h5>
                            <p class="card-text"><strong>{{ $unreadMessages ?? 0 }}</strong> unread messages</p>
                            @if(isset($latestMessage) && $latestMessage)
                            <div class="message-preview mt-2">
                                <small class="text-muted">From: {{ $latestMessage->sender ? $latestMessage->sender->name : 'Unknown' }} - "{{ Str::limit($latestMessage->content ?? '', 30) }}"</small>
                            </div>
                            @endif
                        </div>
                        <div class="card-icon-container">
                            <i class="bi bi-chat-left-text card-icon"></i>
                            @if(($unreadMessages ?? 0) > 0)
                            <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadMessages }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ route('messages.index') }}" class="btn btn-primary">View Messages</a>
                </div>
            </div>
        </div>

        <!-- Appointments -->
        <div class="col">
            <div class="card h-100 shadow-sm dashboard-card appointments-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Appointments</h5>
                            @if(isset($nextAppointment) && $nextAppointment)
                            <p class="card-text"><strong>Next appointment:</strong> {{ \Carbon\Carbon::parse($nextAppointment->AppointmentDate)->format('M d, Y g:i A') }}</p>
                            <div class="appointment-details mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-check"></i> 
                                    Purpose: {{ $nextAppointment->Purpose ?? 'Visit' }} 
                                    <br>
                                    <i class="bi bi-person-badge"></i>
                                    Doctor: {{ $nextAppointment->doctor_name ?? 'Not specified' }}
                                    <br>
                                    <i class="bi bi-person"></i>
                                    Adopter: {{ $nextAppointment->adopter_name ?? 'Not specified' }}
                                    <br>
                                    <i class="bi bi-heart"></i>
                                    Pet: {{ $nextAppointment->PetName ?? 'Not specified' }}
                                </small>
                            </div>
                            @else
                            <p class="card-text">No upcoming appointments</p>
                            @endif
                        </div>
                        <div class="card-icon-container">
                            <i class="bi bi-calendar-event card-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ route('shelter.appointments') }}" class="btn btn-outline-primary">View Calendar</a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col">
            <div class="card h-100 shadow-sm dashboard-card notifications-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Notifications</h5>
                            <p class="card-text">
                                <strong>{{ $publishedNotifications ?? 0 }}</strong> notifications published
                            </p>

                            @if(isset($latestNotification) && $latestNotification)
                            <div class="notification-preview mt-2">
                                <small class="text-muted">
                                    {{ Str::limit($latestNotification->message ?? '', 40) }} - 
                                    {{ $latestNotification->created_at ? \Carbon\Carbon::parse($latestNotification->created_at)->format('M d, Y') : '' }}
                                </small>
                            </div>
                            @endif
                        </div>
                        <div class="card-icon-container">
                            <i class="bi bi-bell card-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- List of Recent Notifications -->
                <ul class="list-group list-group-flush">
                    @forelse($recentNotifications as $notification)
                    <li class="list-group-item">
                        <small class="text-muted">
                            {{ Str::limit($notification->message, 50) }} - 
                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </small>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">No notifications yet.</li>
                    @endforelse
                </ul>

                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ route('shelter.notifications') }}" class="btn btn-primary">View All Notifications</a>
                </div>
            </div>
        </div>



        <!-- Pet Care Resources -->
        <div class="col">
            <div class="card h-100 shadow-sm dashboard-card resources-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Pet Care Resources</h5>
                            <p class="card-text"><strong>{{ $resourceCount ?? 0 }}</strong> resources in your library</p>
                            @if(isset($popularResources) && $popularResources->isNotEmpty())
                            <div class="resources-stats mt-2">
                                <small class="text-muted"><i class="bi bi-journals"></i> Recent: "{{ Str::limit($popularResources->first()->title ?? '', 30) }}"</small>
                            </div>
                            @endif
                        </div>
                        <div class="card-icon-container">
                            <i class="bi bi-journal-text card-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <a href="{{ route('shelter.resources.manage') }}" class="btn btn-outline-primary">Manage Resources</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dashboard-header {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .dashboard-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .card-icon-container {
        position: relative;
    }

    .card-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    /* Card specific styling */
    .pets-card .card-icon {
        color: #4CAF50;
    }

    .adoptions-card .card-icon {
        color: #E91E63;
    }

    .health-card .card-icon {
        color: #2196F3;
    }

    .messages-card .card-icon {
        color: #9C27B0;
    }

    .appointments-card .card-icon {
        color: #FF9800;
    }

    .announcements-card .card-icon {
        color: #FFC107;
    }

    .resources-card .card-icon {
        color: #607D8B;
    }

    .btn-primary {
        background-color: var(--petopia-blue);
        border-color: var(--petopia-blue);
    }

    .btn-primary:hover {
        background-color: var(--petopia-blue-dark);
        border-color: var(--petopia-blue-dark);
    }

    .btn-outline-primary {
        color: var(--petopia-blue);
        border-color: var(--petopia-blue);
    }

    .btn-outline-primary:hover {
        background-color: var(--petopia-blue);
        color: white;
    }

    .notification-badge .badge,
    .card-icon-container .badge {
        font-weight: 500;
    }

    /* Card background subtle patterns */
    .pets-card {
        background-color: #FCFFF5;
        border-left: 4px solid #4CAF50;
    }

    .adoptions-card {
        background-color: #FFF5F8;
        border-left: 4px solid #E91E63;
    }

    .health-card {
        background-color: #F5FBFF;
        border-left: 4px solid #2196F3;
    }

    .messages-card {
        background-color: #F9F5FF;
        border-left: 4px solid #9C27B0;
    }

    .appointments-card {
        background-color: #FFF8F0;
        border-left: 4px solid #FF9800;
    }

    .announcements-card {
        background-color: #FFFDF5;
        border-left: 4px solid #FFC107;
    }

    .resources-card {
        background-color: #F5F7F8;
        border-left: 4px solid #607D8B;
    }
</style>
@endpush
@endsection