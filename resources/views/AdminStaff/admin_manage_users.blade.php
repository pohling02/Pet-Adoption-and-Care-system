@extends('layouts.admin_master')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-primary fw-bold">Manage Users</h2>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @elseif(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr data-role="{{ strtolower($user->role) }}">
                            <td class="ps-3">{{ $index + 1 }}</td>
                            <td>
                                @php

                                $profilePic = null;

                                if (Str::startsWith($user->role, 'shelter_staff') && $user->shelterStaffProfile && $user->shelterStaffProfile->profile_picture) {
                                $pic = Str::replaceFirst('images/', '', $user->shelterStaffProfile->profile_picture);
                                $path = public_path('images/' . $pic);
                                if (file_exists($path)) {
                                $profilePic = asset('images/' . $pic);
                                }
                                } elseif ($user->role === 'adopter' && $user->adopterProfile && $user->adopterProfile->profile_picture) {
                                $pic = Str::replaceFirst('images/', '', $user->adopterProfile->profile_picture);
                                $path = public_path('images/' . $pic);
                                if (file_exists($path)) {
                                $profilePic = asset('images/' . $pic);
                                }
                                }
                                @endphp

                                <div class="d-flex align-items-center">
                                    @if($profilePic)
                                    <div class="rounded-circle overflow-hidden me-2" style="width: 40px; height: 40px;">
                                        <img src="{{ $profilePic }}" alt="Profile Picture" class="img-fluid rounded-circle" style="object-fit: cover; width: 100%; height: 100%;">
                                    </div>
                                    @else
                                    <div class="avatar-sm bg-light rounded-circle text-center fw-bold text-primary me-2 d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div>{{ $user->name }}</div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'admin')
                                <span class="badge bg-danger">Admin</span>
                                @elseif($user->role == 'restricted')
                                <span class="badge bg-warning">Restricted</span>
                                @elseif(Str::startsWith($user->role, 'shelter_staff'))
                                <span class="badge bg-info">Shelter Staff</span>
                                @elseif($user->role == 'adopter')
                                <span class="badge bg-success">Adopter</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <div class="btn-group" role="group">
                                        <!-- View Profile -->
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal{{ $user->UserID }}" title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Restrict / Unrestrict -->
                                        @if($user->role == 'restricted')
                                        <form action="{{ route('admin.unrestrict_user', $user->UserID) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Unrestrict User">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.restrict_user', $user->UserID) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Restrict User">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        </form>
                                        @endif

                                        <!-- Reset Password -->
                                        <form action="{{ route('admin.reset_password', $user->UserID) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.delete_user', $user->UserID) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn" 
                                                    data-bs-toggle="tooltip" title="Delete User"
                                                    data-user-name="{{ $user->name }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@php use Illuminate\Support\Str; @endphp

@foreach($users as $user)
<!-- Profile Modal -->
<div class="modal fade" id="profileModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="profileModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="profileModalLabel{{ $user->UserID }}">
                    <i class="fas fa-user-circle me-2 text-primary"></i>User Profile: {{ $user->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        @php
                        $profileImage = null;

                        if (Str::startsWith($user->role, 'shelter_staff') && $user->shelterStaffProfile && $user->shelterStaffProfile->profile_picture) {
                        $file = Str::replaceFirst('images/', '', $user->shelterStaffProfile->profile_picture);
                        $filePath = public_path('images/' . $file);
                        if (file_exists($filePath)) {
                        $profileImage = asset('images/' . $file);
                        }
                        } elseif ($user->role === 'adopter' && $user->adopterProfile && $user->adopterProfile->profile_picture) {
                        $file = Str::replaceFirst('images/', '', $user->adopterProfile->profile_picture);
                        $filePath = public_path('images/' . $file);
                        if (file_exists($filePath)) {
                        $profileImage = asset('images/' . $file);
                        }
                        }

                        $profileImage = $profileImage ?? asset('images/default-profile.png');
                        @endphp

                        <img src="{{ $profileImage }}" alt="Profile Picture" class="img-fluid rounded-circle border shadow-sm" 
                             style="width: 150px; height: 150px; object-fit: cover;">

                        <h5 class="mt-3">{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>

                        @if($user->role == 'admin')
                        <span class="badge bg-danger">Admin</span>
                        @elseif($user->role == 'restricted')
                        <span class="badge bg-warning">Restricted</span>
                        @elseif(Str::startsWith($user->role, 'shelter_staff'))
                        <span class="badge bg-info">Shelter Staff</span>
                        @elseif($user->role == 'adopter')
                        <span class="badge bg-success">Adopter</span>
                        @else
                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                        @endif
                    </div>


                    <!-- Right column for user details -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-id-card me-2"></i>Personal Information</strong>
                            </div>
                            <div class="card-body">
                                @if($user->role === 'adopter' && $user->adopterProfile)
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Phone:</div>
                                    <div class="col-md-8">{{ $user->adopterProfile->phone_number ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Address:</div>
                                    <div class="col-md-8">{{ $user->adopterProfile->address ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Gender:</div>
                                    <div class="col-md-8">{{ $user->adopterProfile->gender ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Occupation:</div>
                                    <div class="col-md-8">{{ $user->adopterProfile->occupation ?? '-' }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-muted">Bio:</div>
                                    <div class="col-md-8">{{ $user->adopterProfile->bio ?? '-' }}</div>
                                </div>
                                @elseif(Str::startsWith($user->role, 'shelter_staff') && $user->shelterStaffProfile)
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Shelter Name:</div>
                                    <div class="col-md-8">{{ $user->shelterStaffProfile->shelter_name ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Phone:</div>
                                    <div class="col-md-8">{{ $user->shelterStaffProfile->phone_number ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Address:</div>
                                    <div class="col-md-8">{{ $user->shelterStaffProfile->shelter_address ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Position:</div>
                                    <div class="col-md-8">{{ $user->shelterStaffProfile->position ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Gender:</div>
                                    <div class="col-md-8">{{ $user->shelterStaffProfile->gender ?? '-' }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-muted">Bio:</div>
                                    <div class="col-md-8">{{ $user->shelterStaffProfile->bio ?? '-' }}</div>
                                </div>
                                @else
                                <p class="text-muted">No profile information available.</p>
                                @endif
                            </div>
                        </div>

                        @if($user->role === 'adopter' && $user->adopterProfile)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-paw me-2"></i>Adoption Preferences</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Pet Preference:</div>
                                    <div class="col-md-8">{{ $user->adopterProfile->pet_preference ?? '-' }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-muted">Preferred Location:</div>
                                    <div class="col-md-8">{{ $user->adopterProfile->preferred_location ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(Str::startsWith($user->role, 'shelter_staff') && $user->shelterStaffProfile)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-file-alt me-2"></i>Business License</strong>
                            </div>
                            <div class="card-body">
                                @php
                                $license = Str::replaceFirst('images/', '', $user->shelterStaffProfile->business_license);
                                $licensePath = public_path('images/' . $license);
                                $licenseUrl = asset('images/' . $license);
                                @endphp

                                @if($license && file_exists($licensePath))
                                @if(Str::endsWith($license, '.pdf'))
                                <div class="border rounded">
                                    <iframe src="{{ $licenseUrl }}" width="100%" height="300px" class="border-0"></iframe>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ $licenseUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                    </a>
                                </div>
                                @else
                                <div class="text-center">
                                    <img src="{{ $licenseUrl }}" alt="Business License" class="img-fluid border rounded mb-2" style="max-height: 300px;">
                                    <div>
                                        <a href="{{ $licenseUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-search-plus me-1"></i>View Full Size
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @else
                                <p class="text-muted">No business license uploaded.</p>
                                @endif
                            </div>
                        </div>

                        @if($user->shelterStaffProfile->admin_remarks)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-comment-alt me-2"></i>Admin Remarks</strong>
                            </div>
                            <div class="card-body">
                                <div class="p-3 bg-light rounded">
                                    {{ $user->shelterStaffProfile->admin_remarks }}
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif

                        <!-- Add activity section -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-history me-2"></i>Account Activity</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Member Since:</div>
                                    <div class="col-md-8">{{ $user->created_at->format('F d, Y') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Last Updated:</div>
                                    <div class="col-md-8">{{ $user->updated_at->format('F d, Y') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-muted">Status:</div>
                                    <div class="col-md-8">
                                        @if($user->role == 'restricted')
                                        <span class="badge bg-warning">Restricted</span>
                                        @else
                                        <span class="badge bg-success">Active</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteUserName"></strong>? This action cannot be undone.</p>
                <p class="mb-0"><strong>Note:</strong> All data associated with this user will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-1"></i>Delete User
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // User search functionality
        const userSearch = document.getElementById('userSearch');
        if (userSearch) {
            userSearch.addEventListener('keyup', function () {
                const searchTerm = this.value.toLowerCase();
                const userRows = document.querySelectorAll('table tbody tr');

                userRows.forEach(row => {
                    const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const role = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                    if (name.includes(searchTerm) || email.includes(searchTerm) || role.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Role filter functionality with improved accuracy
        const filterButtons = document.querySelectorAll('[data-role]');
        if (filterButtons.length) {
            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Update active state
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const role = this.getAttribute('data-role');
                    const userRows = document.querySelectorAll('table tbody tr');

                    userRows.forEach(row => {
                        const badgeText = row.querySelector('td:nth-child(4) .badge').textContent.toLowerCase();

                        if (role === 'all') {
                            row.style.display = '';
                        } else if (role === 'shelter_staff' && badgeText === 'shelter staff') {
                            row.style.display = '';
                        } else if (badgeText === role) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        }

        // Confirm delete with custom modal
        const deleteButtons = document.querySelectorAll('.delete-btn');
        let currentForm = null;

        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                currentForm = this.closest('form');
                const userName = this.getAttribute('data-user-name');

                // Set user name in confirmation modal
                document.getElementById('deleteUserName').textContent = userName;

                // Show custom confirmation modal
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                confirmModal.show();
            });
        });

        // Handle delete confirmation
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (currentForm) {
                currentForm.submit();
            }
            // Hide modal
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
            confirmModal.hide();
        });
    });
</script>
@endsection