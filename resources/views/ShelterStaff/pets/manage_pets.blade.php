@extends('layouts.shelter_master')

@section('title', 'Manage Pets')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with improved spacing and layout -->
    <div class="row align-items-center mb-4">
        <div class="col-md-4">
            <h2 class="fw-bold text-primary m-0">
                <i class="fas fa-paw me-2"></i> Manage Pets
            </h2>
        </div>

        <!-- Enhanced search with filters -->
        <div class="col-md-5">
            <form action="{{ route('shelter.pets.manage') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search pets..." value="{{ request()->search }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Improved Add Pet button -->
        <div class="col-md-3 text-end">
            @if(auth()->user()->role === 'shelter_staff')
            <a href="{{ route('shelter.pets.create') }}" class="btn btn-success btn-lg shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Add New Pet
            </a>
            @else
            <button class="btn btn-secondary btn-lg" disabled data-bs-toggle="tooltip" title="Approval Pending. Cannot add pets.">
                <i class="fas fa-lock me-1"></i> Add Pet (Pending)
            </button>
            @endif
        </div>
    </div>

    <!-- Stats cards above the table -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Pets</h6>
                            <h3 class="mb-0">{{ $pets->total() }}</h3>
                        </div>
                        <i class="fas fa-paw fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Available</h6>
                            <h3 class="mb-0">{{ $pets->where('AdoptionStatus', 'Available')->count() }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Dogs</h6>
                            <h3 class="mb-0">{{ $pets->where('Species', 'Dog')->count() }}</h3>
                        </div>
                        <i class="fas fa-dog fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Cats</h6>
                            <h3 class="mb-0">{{ $pets->where('Species', 'Cat')->count() }}</h3>
                        </div>
                        <i class="fas fa-cat fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pets->count() > 0)
    <!-- Modern table with enhanced visual design -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th width="50px">#</th>
                        <th width="80px">Photo</th>
                        <th>Pet Name</th>
                        <th>Species</th>
                        <th>Breed</th>
                        <th>Health Condition</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pets as $index => $pet)
                    <tr>
                        <td><strong>{{ $index + 1 }}</strong></td>
                        <td>
                            <!-- Pet thumbnail with appropriate icon if no image -->
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                @if($pet->images && $pet->images->isNotEmpty() && $pet->images->first()->ImagePath)
                                <img src="{{ asset($pet->images->first()->ImagePath) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $pet->PetName }}">
                                @elseif(isset($pet->PhotoURL))
                                <img src="{{ $pet->PhotoURL }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $pet->PetName }}">
                                @else
                                <img src="{{ asset('images/default_pet.png') }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $pet->PetName }}">
                                @endif
                            </div>
                        </td>
                        <td class="fw-bold text-primary">{{ $pet->PetName }}</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $pet->Species == 'Dog' ? 'info' : ($pet->Species == 'Cat' ? 'warning' : 'secondary') }} text-white px-3 py-2">
                                {{ $pet->Species }}
                            </span>
                        </td>
                        <td>{{ $pet->Breed }}</td>
                        <td>
                            @switch(strtolower($pet->HealthCondition ?? 'healthy'))
                            @case('sick')
                            <span class="badge bg-warning text-dark">Sick</span>
                            @break
                            @case('critical')
                            <span class="badge bg-danger">Critical</span>
                            @break
                            @default
                            <span class="badge bg-success">Healthy</span>
                            @endswitch
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($pet->DateOfBirth)->diff(\Carbon\Carbon::now())->y }} years 
                            {{ \Carbon\Carbon::parse($pet->DateOfBirth)->diff(\Carbon\Carbon::now())->m }} months
                        </td>
                        <td>
                            <span class="badge {{ $pet->AdoptionStatus == 'Available' ? 'bg-success' : 'bg-danger' }} text-white px-3 py-2">
                                {{ $pet->AdoptionStatus == 'Available' ? 'Available' : 'Not Available' }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group">
                                <a href="{{ route('shelter.pets.view', ['id' => $pet->PetID]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('shelter.pets.edit', ['id' => $pet->PetID]) }}" class="btn btn-info btn-sm text-white">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $pet->PetID }}">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Delete Modal for each pet -->
                    <div class="modal fade"
                         id="deleteModal{{ $pet->PetID }}"
                         tabindex="-1"
                         aria-labelledby="deleteModalLabel{{ $pet->PetID }}"
                         aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $pet->PetID }}">
                              Delete Pet
                            </h5>
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to delete
                            <strong>{{ $pet->PetName }}</strong>?
                          </div>
                          <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                              Cancel
                            </button>
                            <form action="{{ route('shelter.pets.destroy', $pet->PetID) }}"
                                  method="POST"
                                  class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="submit"
                                      class="btn btn-danger">
                                Yes, Delete
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Showing <strong>{{ $pets->firstItem() ?? 0 }}</strong> to <strong>{{ $pets->lastItem() ?? 0 }}</strong> of <strong>{{ $pets->total() }}</strong> pets
        </div>
        <nav aria-label="Pet navigation">
            <ul class="pagination">
                <li class="page-item {{ $pets->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $pets->url(1) }}" aria-label="First">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>

                <li class="page-item {{ $pets->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $pets->previousPageUrl() }}" aria-label="Previous">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>

                @php
                $start = max($pets->currentPage() - 2, 1);
                $end = min($start + 4, $pets->lastPage());
                $start = max(min($end - 4, $start), 1);
                @endphp

                @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $pets->url(1) }}">1</a>
                </li>
                @if($start > 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $pets->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $pets->url($i) }}">{{ $i }}</a>
                </li>
                @endfor

                @if($end < $pets->lastPage())
                @if($end < $pets->lastPage() - 1)
                <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $pets->url($pets->lastPage()) }}">{{ $pets->lastPage() }}</a>
                </li>
                @endif

                <li class="page-item {{ $pets->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $pets->nextPageUrl() }}" aria-label="Next">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>

                <li class="page-item {{ $pets->currentPage() == $pets->lastPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $pets->url($pets->lastPage()) }}" aria-label="Last">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @else
    <div class="card shadow-sm border-warning">
        <div class="card-body text-center py-5">
            <i class="fas fa-search fa-3x text-warning mb-3"></i>
            <h4>No Pets Found</h4>
            <p class="mb-0">We couldn't find any pets matching your search criteria.</p>
            <a href="{{ route('shelter.pets.manage') }}" class="btn btn-outline-primary mt-3">
                <i class="fas fa-sync-alt me-1"></i> Clear Search
            </a>
        </div>
    </div>
    @endif
</div>

<style>
    .pagination {
        margin-bottom: 0;
    }

    .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .page-link {
        color: #007bff;
        padding: 0.5rem 0.75rem;
    }

    .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        // Status toggle functionality
        const statusToggles = document.querySelectorAll('input[role="switch"]');
        statusToggles.forEach(toggle => {
            toggle.addEventListener('change', function () {
                const petId = this.dataset.petId;
                const newStatus = this.checked ? 'Available' : 'Not Available';
                // Here you would typically make an AJAX request to update the status
                console.log(`Updating pet #${petId} status to ${newStatus}`);
                // Update the badge next to the toggle
                const badgeEl = this.nextElementSibling.querySelector('.badge');
                badgeEl.textContent = newStatus;
                badgeEl.className = `badge ${newStatus === 'Available' ? 'bg-success' : 'bg-danger'} text-white px-3 py-2`;
            });
        });
    });
</script>
@endsection