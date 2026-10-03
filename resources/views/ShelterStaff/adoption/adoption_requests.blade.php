@extends('layouts.shelter_master')

@section('title', 'Adoption Requests')

@section('content')
<div class="container-fluid mt-4">
    <!-- Page Header with Search and Filter -->
    <form method="GET" action="{{ route('shelter.adoptions') }}">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Adoption Requests</h2>

            <div class="d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search requests...">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <!-- Dropdown auto-submits on change -->
                <select name="status" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="Under Review" {{ request('status') == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Table Layout -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if(count($adoptions) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="fw-semibold ps-4">No</th>
                            <th scope="col" class="fw-semibold">Adopter Name</th>
                            <th scope="col" class="fw-semibold">Pet Name</th>
                            <th scope="col" class="fw-semibold">Request Date</th>
                            <th scope="col" class="fw-semibold">Status</th>
                            <th scope="col" class="fw-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adoptions as $index => $adoption)
                        <tr>
                            <td class="ps-4">{{ $adoptions->firstItem() + $index }}</td>
                            <td>{{ $adoption->adopter->name ?? 'Unknown' }}</td>
                            <td>{{ $adoption->pet->PetName ?? 'Unknown' }}</td>
                            <td>{{ \Carbon\Carbon::parse($adoption->ApplicationDate)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge rounded-pill 
                                      @if($adoption->AdoptionStatus == 'Approved') bg-success 
                                      @elseif($adoption->AdoptionStatus == 'Rejected') bg-danger 
                                      @elseif($adoption->AdoptionStatus == 'Cancelled') bg-secondary
                                      @else bg-warning @endif">
                                    {{ $adoption->AdoptionStatus }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewAdoption{{ $adoption->ApplicationID }}">
                                    Review
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center py-3">
                {{ $adoptions->links() }}
            </div>
            @else
            <div class="alert alert-info text-center m-4 p-5">
                <i class="bi bi-inbox-fill display-1"></i>
                <h3 class="mt-3">No Adoption Requests</h3>
                <p class="text-muted">When adopters apply to adopt your pets, requests will appear here.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Adoption Details Modal -->
@foreach($adoptions as $adoption)
<div class="modal fade" id="viewAdoption{{ $adoption->ApplicationID }}" tabindex="-1" aria-labelledby="modalLabel{{ $adoption->ApplicationID }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-clipboard-check me-2"></i>Adoption Request Details #{{ sprintf('%04d', $adoption->ApplicationID) }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Status Banner -->
                <div class="alert 
                     @if($adoption->AdoptionStatus == 'Approved') alert-success
                     @elseif($adoption->AdoptionStatus == 'Rejected') alert-danger
                     @elseif($adoption->AdoptionStatus == 'Cancelled') alert-secondary
                     @else alert-warning @endif">
                    <div class="d-flex align-items-center">
                        <i class="bi 
                           @if($adoption->AdoptionStatus == 'Approved') bi-check-circle-fill
                           @elseif($adoption->AdoptionStatus == 'Rejected') bi-x-circle-fill
                           @elseif($adoption->AdoptionStatus == 'Cancelled') bi-slash-circle-fill
                           @else bi-hourglass-split @endif me-2 fs-5"></i>
                        <span class="fw-bold">Status: {{ $adoption->AdoptionStatus }}</span>
                        <span class="ms-auto">Application Date: {{ \Carbon\Carbon::parse($adoption->ApplicationDate)->format('d/m/Y') }}</span>
                    </div>
                    
                    <!-- Add this section to display cancellation reason -->
                    @if($adoption->AdoptionStatus == 'Cancelled' && $adoption->CancellationReason)
                    <div class="mt-2 border-top pt-2">
                        <strong>Cancellation Reason:</strong> {{ $adoption->CancellationReason }}
                    </div>
                    @endif
                </div>

                <div class="row g-4">
                    <!-- First row: Adopter & Pet details -->
                    <div class="col-md-6">
                        <!-- Adopter Details -->
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-person-circle me-2"></i>Adopter Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Full Name:</strong> {{ $adoption->FullName }}</p>
                                <p><strong>Email:</strong> {{ $adoption->Email }}</p>
                                <p><strong>Phone:</strong> {{ $adoption->ContactNumber }}</p>
                                <p><strong>Age:</strong> {{ $adoption->Age }}</p>
                                <p><strong>Gender:</strong> {{ $adoption->Gender }}</p>
                                <p><strong>Occupation:</strong> {{ $adoption->Occupation }}</p>
                                <p><strong>Address:</strong> {{ $adoption->Address }}</p>
                                <p><strong>State:</strong> {{ $adoption->State }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Pet Details -->
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-heart-fill me-2 text-danger"></i>Pet Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    @if ($adoption->pet->images->isNotEmpty())
                                    <img src="{{ asset('images/' . basename($adoption->pet->images->first()->ImagePath)) }}" 
                                         alt="Pet Image" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                    @else
                                    <img src="{{ asset('images/default-pet.jpg') }}" alt="Default Pet Image" class="img-fluid rounded" style="max-height: 150px;">
                                    @endif
                                </div>
                                <p><strong>Name:</strong> {{ $adoption->pet->PetName }}</p>
                                <p><strong>Species:</strong> {{ $adoption->pet->Species }}</p>
                                <p><strong>Breed:</strong> {{ $adoption->pet->Breed }}</p>
                                <p><strong>Age:</strong> {{ $adoption->pet->Age ?? 'Unknown' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Second row: Household & Pet Care -->
                    <div class="col-md-6">
                        <!-- Household Details -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-house me-2"></i>Household Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="fw-bold">Household Details:</h6>
                                    <p class="border-start border-primary ps-3">{{ $adoption->HouseholdDetails ?? 'Not Provided' }}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Other Pets:</h6>
                                    <p class="border-start border-primary ps-3">{{ $adoption->OtherPetsInfo ?? 'Not Provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Reference Information -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-person-check me-2"></i>Reference Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Reference Name:</strong> {{ $adoption->ReferenceName ?? 'Not Provided' }}</p>
                                <p><strong>Reference Contact:</strong> {{ $adoption->ReferenceContact ?? 'Not Provided' }}</p>
                                <p><strong>Relationship:</strong> {{ $adoption->ReferenceRelationship ?? 'Not Provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Third row: Adoption reasons & care plans -->
                    <div class="col-12">
                        <!-- Adoption Reason & Care Plan -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-file-earmark-text me-2"></i>Adoption Reason & Care Plan
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="fw-bold">Reason for Adoption:</h6>
                                    <p class="border-start border-primary ps-3">{{ $adoption->ReasonForAdoption ?? 'Not Provided' }}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Pet Care Plan:</h6>
                                    <p class="border-start border-primary ps-3">{{ $adoption->PetCarePlan ?? 'Not Provided' }}</p>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Emergency Plan:</h6>
                                    <p class="border-start border-primary ps-3">{{ $adoption->EmergencyPlan ?? 'Not Provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fourth row: Living environment photos -->
                    <div class="col-12">
                        <!-- Living Environment Photos -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-house-heart me-2"></i>Living Environment Photos
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($adoption->photos->count() > 0)
                                <div class="row g-3">
                                    @foreach($adoption->photos as $photo)
                                    <div class="col-md-4">
                                        <a href="{{ asset('storage/' . $photo->PhotoPath) }}" target="_blank" class="d-block">
                                            <img src="{{ asset('storage/' . $photo->PhotoPath) }}" 
                                                 class="img-fluid rounded shadow-sm" 
                                                 style="width: 100%; height: 150px; object-fit: cover;">
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="bi bi-image text-muted fs-1"></i>
                                    <p class="text-muted mt-2">No photos uploaded.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Fifth row: Resubmission details -->
                    <div class="col-12">
                        <!-- Resubmission Details -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-arrow-repeat me-2"></i>Resubmission Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                 style="width: {{ ($adoption->ResubmissionCount / 3) * 100 }}%;" 
                                                 aria-valuenow="{{ $adoption->ResubmissionCount }}" aria-valuemin="0" aria-valuemax="3">
                                                {{ $adoption->ResubmissionCount }}/3
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <span class="badge bg-primary">{{ 3 - $adoption->ResubmissionCount }} remaining</span>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    @if($adoption->ResubmissionCount >= 3)
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        This adopter has reached the maximum number of resubmissions.
                                    </div>
                                    @elseif($adoption->LastRejectionDate && \Carbon\Carbon::parse($adoption->LastRejectionDate)->diffInDays(now()) < 7)
                                    <div class="alert alert-warning">
                                        <i class="bi bi-clock-history me-2"></i>
                                        The adopter must wait {{ (int)(7 - \Carbon\Carbon::parse($adoption->LastRejectionDate)->diffInDays(now())) }} days before resubmitting.
                                    </div>
                                    @else
                                    <div class="alert alert-success">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        This adopter can resubmit.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Cancellation Details Card if status is Cancelled -->
                    @if($adoption->AdoptionStatus == 'Cancelled')
                    <div class="col-12">
                        <!-- Cancellation Details -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="card-title m-0">
                                    <i class="bi bi-slash-circle me-2"></i>Cancellation Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-secondary">
                                    <h6 class="fw-bold">Cancellation Reason:</h6>
                                    <p class="border-start border-secondary ps-3">{{ $adoption->CancellationReason ?? 'No reason provided' }}</p>
                                    <p class="text-muted small mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        This application was cancelled by the adopter.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer">
                @if(in_array($adoption->AdoptionStatus, ['Pending', 'Rejected', 'Under Review']))
                <form action="{{ route('shelter.approve_adoption', $adoption->ApplicationID) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"
                            onclick="return confirm('Are you sure you want to approve this adoption request?');">
                        <i class="bi bi-check-circle me-1"></i> Approve
                    </button>
                </form>

                <!-- Add the Reject button to open the rejection modal -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $adoption->ApplicationID }}">
                    <i class="bi bi-x-circle me-1"></i> Reject
                </button>
                @else
                <button class="btn btn-secondary" disabled>
                    <i class="bi bi-check2-all me-1"></i> Processed
                </button>
                @endif
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal{{ $adoption->ApplicationID }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $adoption->ApplicationID }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel{{ $adoption->ApplicationID }}">
                    <i class="bi bi-exclamation-triangle me-2"></i>Reject Adoption Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('shelter.reject_adoption', $adoption->ApplicationID) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="staffNote" class="form-label fw-bold">Rejection Reason:</label>
                        <textarea class="form-control" id="staffNote" name="staffNote" rows="4" required placeholder="Please provide a reason for rejecting this adoption application..."></textarea>
                        <small class="text-muted">This reason will be shared with the adopter to help them understand why their application was rejected.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <span>The adopter will be notified via email and in-app notification about this rejection.</span>
                        @if($adoption->ResubmissionCount < 3)
                        <div class="mt-2">They will be able to resubmit in 7 days ({{ 3 - $adoption->ResubmissionCount }} resubmissions remaining).</div>
                        @else
                        <div class="mt-2 text-danger">This is their final rejection. They cannot resubmit.</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection