@extends('layouts.shelter_master')
@section('title', 'Manage Appointments')
@section('content')
<div class="dashboard-container">
    <!-- Page Header with Actions -->
    <div class="dashboard-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Manage Appointments</h4>
        <div>
            <a href="{{ route('shelter.appointment.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>New Appointment
            </a>
            <div class="btn-group ms-2">
                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">All Appointments</a></li>
                    <li><a class="dropdown-item" href="#">Upcoming</a></li>
                    <li><a class="dropdown-item" href="#">Shelter Booked</a></li>
                    <li><a class="dropdown-item" href="#">Adopter Booked</a></li>
                    <li><a class="dropdown-item" href="#">Expired</a></li>
                    <li><a class="dropdown-item" href="#">Cancelled</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Search and Date Filter -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('shelter.appointments') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search appointments..." 
                                   value="{{ request('search') }}" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-calendar text-muted"></i>
                            </span>
                            <input type="date" name="date" class="form-control border-start-0" placeholder="Date range" 
                                   value="{{ request('date') }}" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="filter" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Filter --</option>
                            <option value="Upcoming" {{ request('filter') === 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="Shelter Booked" {{ request('filter') === 'Shelter Booked' ? 'selected' : '' }}>Shelter Booked</option>
                            <option value="Adopter Booked" {{ request('filter') === 'Adopter Booked' ? 'selected' : '' }}>Adopter Booked</option>
                            <option value="Expired" {{ request('filter') === 'Expired' ? 'selected' : '' }}>Expired</option>
                            <option value="Cancelled" {{ request('filter') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <!-- Optional: Remove the submit button if auto-submit is preferred -->
                    <div class="col-md-2 text-end">
                        <a href="{{ route('shelter.appointments') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table with Modern Styling -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Booker</th>
                            <th>Pet</th>
                            <th>Doctor</th>
                            <th>Appointment Date</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                        <tr>
                            <td class="ps-4">
                                @if(isset($appointment->adopter))
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-light text-primary me-2">
                                        {{ substr($appointment->adopter->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span>{{ $appointment->adopter->name }}</span>
                                        <span class="badge bg-info text-white ms-2">Adopter</span>
                                    </div>
                                </div>
                                @else
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-light text-secondary me-2">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div>
                                        <span>Shelter Staff</span>
                                        <span class="badge bg-secondary text-white ms-2">Shelter</span>
                                    </div>
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="d-flex align-items-center">
                                    <span class="pet-icon me-2">🐾</span>
                                    {{ $appointment->pet->PetName }}
                                </span>
                            </td>
                            <td>
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-user-md text-muted me-2"></i>
                                    {{ $appointment->doctor->DoctorName }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ \Carbon\Carbon::parse($appointment->AppointmentDate)->format('d M Y') }}</span>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($appointment->AppointmentDate)->format('h:i A') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark rounded-pill purpose-badge">
                                    <i class="fas fa-clipboard-list me-1"></i>
                                    <span class="purpose-text">{{ $appointment->Purpose }}</span>
                                </span>
                            </td>
                            <td>
                                @if($appointment->Status === 'Cancelled')
                                <span class="badge bg-danger rounded-pill px-3">Cancelled</span>
                                @elseif($appointment->Status === 'Rescheduled')
                                <span class="badge bg-warning text-dark rounded-pill px-3">Rescheduled</span>
                                @elseif($appointment->Status === 'Expired')
                                <span class="badge bg-secondary rounded-pill px-3">Expired</span>
                                @else
                                <span class="badge bg-success rounded-pill px-3">Confirmed</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if(!isset($appointment->adopter))
                                        @if($appointment->Status !== 'Cancelled')
                                        <li>
                                            <a class="dropdown-item" 
                                               href="{{ route('shelter.appointment.reschedule.form', ['appointment' => $appointment->AppointmentID]) }}">
                                                <i class="fas fa-calendar-alt me-2"></i>Reschedule
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('shelter.appointment.cancel', ['appointment' => $appointment->AppointmentID]) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-times-circle me-2"></i>Cancel
                                                </button>
                                            </form>
                                        </li>
                                        @else
                                        <li><span class="dropdown-item text-muted"><i class="fas fa-info-circle me-2"></i>Appointment already cancelled</span></li>
                                        @endif
                                        @else
                                        <!-- Adopter-booked appointments: limited control -->
                                        <li>
                                            <a class="dropdown-item" 
                                               href="{{ route('shelter.appointment.contact.adopter', ['appointment' => $appointment->AppointmentID]) }}">
                                                <i class="fas fa-envelope me-2"></i>Contact Adopter
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        @if($appointment->Status !== 'Cancelled' && $appointment->Status !== 'Emergency Override')
                                        <li>
                                            <a class="dropdown-item text-warning" 
                                               href="{{ route('shelter.appointment.emergency.override', ['appointment' => $appointment->AppointmentID]) }}"
                                               onclick="return confirm('Emergency override should only be used in urgent situations. An automatic notification will be sent to the adopter. Continue?');">
                                                <i class="fas fa-exclamation-triangle me-2"></i>Emergency Override
                                            </a>
                                        </li>
                                        @endif
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5>No appointments found</h5>
                                    <p class="text-muted">There are no appointments scheduled at this time.</p>
                                    <a href="{{ route('shelter.appointment.create') }}" class="btn btn-primary mt-2">
                                        Schedule New Appointment
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($appointments->count() > 0)
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Showing <span class="fw-bold">{{ $appointments->firstItem() }}-{{ $appointments->lastItem() }}</span> of 
            <span class="fw-bold">{{ $appointments->total() }}</span> appointments
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm">
                {{-- Previous Page Link --}}
                @if ($appointments->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link" href="{{ $appointments->previousPageUrl() }}" rel="prev" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                @endif

                {{-- Page Number Links --}}
                @foreach ($appointments->getUrlRange(max(1, $appointments->currentPage() - 2), min($appointments->lastPage(), $appointments->currentPage() + 2)) as $page => $url)
                @if ($page == $appointments->currentPage())
                <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($appointments->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $appointments->nextPageUrl() }}" rel="next" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                @else
                <li class="page-item disabled">
                    <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>

<style>
    .dashboard-container {
        padding: 1.5rem;
        background-color: #f8f9fa;
    }
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem;
    }
    .table th {
        font-weight: 600;
        color: #495057;
    }
    .badge {
        font-weight: 500;
    }
    .pet-icon {
        font-size: 1.2rem;
    }
    .pagination {
        margin-bottom: 0;
    }
    .page-item.active .page-link {
        background-color: #3b5998;
        border-color: #3b5998;
    }
    .page-link {
        color: #3b5998;
        padding: 0.375rem 0.75rem;
    }
    .page-link:hover {
        color: #2d4373;
        background-color: #e9ecef;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
    }
    .badge {
        font-weight: 500;
        padding: 0.4rem 0.8rem;
    }
    .badge i {
        font-size: 0.8rem;
    }
    .purpose-badge {
        border: 1px solid rgba(0,0,0,0.1);
        padding: 0.4rem 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        max-width: 100%;
    }

    .purpose-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
        display: inline-block;
    }

    .purpose-badge:hover .purpose-text {
        white-space: normal;
        overflow: visible;
        position: relative;
        z-index: 5;
        background-color: #f8f9fa;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border-radius: 4px;
        padding: 2px 4px;
    }
</style>
@endsection