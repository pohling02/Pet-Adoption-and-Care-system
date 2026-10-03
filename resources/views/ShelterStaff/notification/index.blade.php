@extends('layouts.shelter_master')
@section('title', 'Notifications')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h2 class="mb-0 fs-4">
                        <i class="fa fa-bullhorn me-2"></i> Notifications
                    </h2>
                    <button class="btn btn-light d-flex align-items-center" id="publishBtn">
                        <i class="fa fa-plus me-2"></i> Publish New Announcement
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Message</th>
                                    <th class="py-3">Type</th>
                                    <th class="py-3">Details</th>
                                    <th class="py-3">Posted On</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                    <tr>
                                        <td class="py-3">{{ $notification->message }}</td>
                                        <td class="py-3">
                                            @if($notification->type == 'adoption_rejection')
                                                <span class="badge rounded-pill bg-danger">Adoption Rejection</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">{{ ucfirst($notification->type) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($notification->type == 'adoption_rejection' && isset($notification->data['rejection_reason']))
                                                <button type="button" class="btn btn-sm btn-info view-details" 
                                                    data-bs-toggle="modal" data-bs-target="#detailsModal" 
                                                    data-details="{{ json_encode($notification->data) }}">
                                                    <i class="fa fa-eye me-1"></i> View Details
                                                </button>
                                            @endif
                                        </td>
                                        <td class="py-3">{{ $notification->created_at->format('M d, Y') }}</td>
                                        <td class="py-3">
                                            @if($notification->read_at)
                                                <span class="badge rounded-pill bg-success">Read</span>
                                            @else
                                                <span class="badge rounded-pill bg-warning text-dark">Unread</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="btn-group" role="group">
                                                @if(!$notification->read_at)
                                                    <form action="{{ route('notifications.markRead', $notification->NotificationID) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary me-1">
                                                            <i class="fa fa-check me-1"></i> Mark as Read
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if($notification->type != 'adoption_rejection')
                                                    <a href="{{ route('shelter.announcements.edit', $notification->NotificationID) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="fa fa-edit me-1"></i> Edit
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fa fa-bell-slash fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No notifications available</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailsModalLabel">Adoption Rejection Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Pet:</strong> <span id="petName"></span>
                </div>
                <div class="mb-3">
                    <strong>Application ID:</strong> <span id="applicationId"></span>
                </div>
                <div class="mb-3">
                    <strong>Rejection Reason:</strong>
                    <p id="rejectionReason" class="p-2 border rounded bg-light"></p>
                </div>
                <div class="mb-3">
                    <strong>Can Resubmit:</strong> <span id="canResubmit"></span>
                </div>
                <div class="mb-3" id="resubmitDateContainer">
                    <strong>Resubmit Date:</strong> <span id="resubmitDate"></span>
                </div>
                <div class="mb-3">
                    <strong>Attempts Remaining:</strong> <span id="attemptsRemaining"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const publishBtn = document.getElementById('publishBtn');
    publishBtn.addEventListener('click', function() {
        window.location.href = "{{ route('shelter.announcements.create') }}";
    });
    
    // Add row hover effect
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseover', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseout', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Modal detail population
    const viewDetailButtons = document.querySelectorAll('.view-details');
    viewDetailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const details = JSON.parse(this.getAttribute('data-details'));
            
            document.getElementById('petName').textContent = details.pet_name || 'N/A';
            document.getElementById('applicationId').textContent = details.application_id || 'N/A';
            document.getElementById('rejectionReason').textContent = details.rejection_reason || 'N/A';
            document.getElementById('canResubmit').textContent = details.can_resubmit ? 'Yes' : 'No';
            document.getElementById('attemptsRemaining').textContent = details.attempts_remaining || '0';
            
            const resubmitDateContainer = document.getElementById('resubmitDateContainer');
            if (details.can_resubmit && details.resubmit_date) {
                resubmitDateContainer.style.display = 'block';
                document.getElementById('resubmitDate').textContent = details.resubmit_date;
            } else {
                resubmitDateContainer.style.display = 'none';
            }
        });
    });
});
</script>
@endsection