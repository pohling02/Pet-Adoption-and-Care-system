@extends('layouts.shelter_master')
@section('title', 'Edit Announcement')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <a href="{{ route('shelter.notifications') }}" class="btn btn-outline-light btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back
                    </a>
                    <h2 class="mb-0 fs-4">
                        <i class="fa fa-edit me-2"></i> Edit Announcement
                    </h2>
                    <div></div> <!-- Empty div for flex spacing -->
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <!-- Edit Announcement Form -->
                            <form action="{{ route('shelter.announcements.update', $announcement->NotificationID) }}" method="POST" class="border rounded p-4 bg-light shadow-sm">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Subject:</label>
                                    <input type="text" class="form-control" name="subject" value="{{ strtok($announcement->message, '-') }}" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Message:</label>
                                    <textarea class="form-control" name="message" rows="5" required>{{ substr($announcement->message, strpos($announcement->message, '-') + 2) }}</textarea>
                                    <div class="form-text">Be clear and concise in your message to ensure recipients understand the announcement.</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <span class="text-muted small">
                                            <i class="fa fa-clock me-1"></i> Originally posted: {{ $announcement->created_at->format('M d, Y, g:i A') }}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('shelter.notifications') }}" class="btn btn-outline-secondary">
                                            <i class="fa fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save me-1"></i> Update Announcement
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message Card (Hidden by default) -->
            <div class="card shadow-sm mt-4 d-none" id="successCard">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fa fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="mb-3">Announcement Updated Successfully!</h3>
                    <p class="text-muted mb-4">Your announcement has been successfully updated and will be visible to recipients.</p>
                    <div>
                        <a href="{{ route('shelter.notifications') }}" class="btn btn-primary">
                            <i class="fa fa-list me-1"></i> View All Notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a success message
    const successAlert = document.querySelector('.alert-success');
    const successCard = document.getElementById('successCard');
    const mainCard = document.querySelector('.card:not(#successCard)');
    
    if (successAlert) {
        // Hide the main card and show the success card
        setTimeout(() => {
            mainCard.classList.add('d-none');
            successCard.classList.remove('d-none');
        }, 100);
    }
});
</script>
@endsection