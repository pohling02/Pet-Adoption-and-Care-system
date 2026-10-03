@extends('layouts.admin_master')

@section('title', 'Approve Shelter Staff Requests')

@section('content')
<div class="container mt-5">
    <h2 class="text-center fw-bold mb-4">Approve Shelter Staff Requests</h2>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @elseif(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="border">No.</th>
                    <th class="border">Name</th>
                    <th class="border">Email</th>
                    <th class="border">Role</th>
                    <th class="border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingShelters as $index => $user)
                <tr>
                    <td class="border">{{ $index + 1 }}</td> <!-- Auto-increment number -->
                    <td class="border">{{ $user->name }}</td>
                    <td class="border">{{ $user->email }}</td>
                    <td class="border"><strong>Pending Approval</strong></td>
                    <td class="border">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#profileModal{{ $user->UserID }}">
                            View Profile
                        </button>
                        <form action="{{ route('admin.approve_shelter', $user->UserID) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" title="Approve"><i class="fas fa-check"></i> Approve</button>
                        </form>
                        <!-- Replace the reject form/button with just a button to open the modal -->
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->UserID }}">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No pending shelter staff requests.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@php use Illuminate\Support\Str; @endphp

@foreach($pendingShelters as $user)
<!-- Modal -->
<div class="modal fade" id="profileModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="profileModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="profileModalLabel{{ $user->UserID }}">Shelter Staff Profile: {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if($user->shelterStaffProfile)
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Shelter Name:</strong> {{ $user->shelterStaffProfile->shelter_name ?? '-' }}</p>
                <p><strong>Phone Number:</strong> {{ $user->shelterStaffProfile->phone_number ?? '-' }}</p>
                <p><strong>Shelter Address:</strong> {{ $user->shelterStaffProfile->shelter_address ?? '-' }}</p>
                <p><strong>Position:</strong> {{ $user->shelterStaffProfile->position ?? '-' }}</p>
                <p><strong>Gender:</strong> {{ $user->shelterStaffProfile->gender ?? '-' }}</p>
                <p><strong>Bio:</strong> {{ $user->shelterStaffProfile->bio ?? '-' }}</p>

                @php
                $licenseFile = $user->shelterStaffProfile->business_license;
                $normalizedPath = Str::replaceFirst('images/', '', $licenseFile); // remove extra "images/" if present
                $licensePath = public_path('images/' . $normalizedPath);
                $licenseUrl = asset('images/' . $normalizedPath);
                @endphp


                <p><strong>Business License:</strong></p>
                @if($licenseFile && file_exists($licensePath))
                @if(Str::endsWith($licenseFile, '.pdf'))
                <iframe src="{{ $licenseUrl }}" width="100%" height="400px" class="border rounded"></iframe>
                @else
                <img src="{{ $licenseUrl }}" alt="Business License" class="img-fluid border rounded mb-3" style="max-height: 300px;">
                @endif
                @else
                <p class="text-danger">No business license uploaded or file not found.</p>
                <p><small class="text-muted">Expected path: {{ $licensePath }}</small></p>
                @endif


                @php
                $profilePic = $user->shelterStaffProfile->profile_picture;
                $normalizedProfilePic = Str::replaceFirst('images/', '', $profilePic);
                $profilePath = public_path('images/' . $normalizedProfilePic);
                $profileUrl = asset('images/' . $normalizedProfilePic);
                @endphp

                <p><strong>Profile Picture:</strong></p>
                @if($profilePic && file_exists($profilePath))
                <img src="{{ $profileUrl }}" alt="Profile Picture" class="img-fluid rounded border" style="max-height: 250px;">
                @else
                <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile" class="img-fluid rounded border" style="max-height: 250px;">
                @endif



                {{-- Admin Remarks (optional) --}}
                @if($user->shelterStaffProfile->admin_remarks)
                <div class="alert alert-info mt-3">
                    <strong>Previous Admin Remarks:</strong><br>
                    {{ $user->shelterStaffProfile->admin_remarks }}
                </div>
                @endif

                @else
                <p class="text-danger">No profile information found for this user.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@foreach($pendingShelters as $user)
<!-- Reject Modal -->
<div class="modal fade" id="rejectModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel{{ $user->UserID }}">Reject Shelter Staff Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.reject_shelter', $user->UserID) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Rejection Reason:</label>
                        <textarea class="form-control" name="reject_reason" id="reject_reason" rows="4" required></textarea>
                        <div class="form-text">Please provide a reason for rejecting this request. This will be saved in the system.</div>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();
                const form = this.closest("form");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This action will delete the request permanently!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, reject it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
