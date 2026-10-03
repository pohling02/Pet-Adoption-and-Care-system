@extends('layouts.adopter_master')

@section('title', 'Notifications')

@section('content')

<!-- Custom CSS for Blue Header -->
<style>
    .bg-custom-blue {
        background-color: #1F4F99 !important;
        color: #fff !important;
    }
</style>

<div class="container mt-4">
    <div class="card shadow-lg">
        <!-- Header using the custom blue color -->
        <div class="card-header bg-custom-blue d-flex justify-content-between align-items-center">
            <h4 class="m-0"><i class="fas fa-bell"></i> Notifications</h4>
            <a href="{{ route('notifications.markAllRead') }}" class="btn btn-light btn-sm">
                <i class="fas fa-check-double"></i> Mark All as Read
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($notifications->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-3x"></i>
                <p class="mt-2">No new notifications</p>
            </div>
            @else
            <div class="list-group">
                @foreach($notifications as $notification)
                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center notification-row {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                    <div class="notification-link" 
                         data-message="{{ $notification->message }}" 
                         data-url="{{ route('notifications.markRead', $notification->NotificationID) }}"
                         data-details="{{ $notification->type == 'adoption_rejection' ? json_encode($notification->data) : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="fas {{ $notification->read_at ? 'fa-envelope-open text-secondary' : 'fa-envelope text-primary' }} me-3"></i>
                            <div>
                                <div>{{ $notification->message }}</div>
                                @if($notification->type == 'adoption_rejection' && isset($notification->data['rejection_reason']))
                                <small class="text-danger">Reason: {{ $notification->data['rejection_reason'] }}</small>
                                @endif
                                <small class="text-muted">{{ $notification->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-danger notification-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal for Viewing Notifications -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header uses the custom blue color -->
            <div class="modal-header bg-custom-blue">
                <h5 class="modal-title" id="notificationModalLabel"><i class="fas fa-bell"></i> Notification</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="notificationMessage"></p>
                <div id="notificationDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to Handle Notification Read and Delete Actions -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const notificationLinks = document.querySelectorAll(".notification-link");

        notificationLinks.forEach(link => {
            link.addEventListener("click", function () {
                const message = this.getAttribute("data-message");
                const markReadUrl = this.getAttribute("data-url");
                const notificationRow = this.closest(".notification-row");
                const notificationData = JSON.parse(this.getAttribute("data-details") || "{}");
                document.getElementById("notificationMessage").innerText = message;
                const detailsDiv = document.getElementById("notificationDetails");
                if (notificationData && notificationData.rejection_reason) {
                    detailsDiv.innerHTML = `<p class="mt-3"><strong>Rejection Reason:</strong> ${notificationData.rejection_reason}</p>`;
                } else {
                    detailsDiv.innerHTML = '';
                }

                const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
                notificationModal.show();
                if (markReadUrl) {
                    fetch(markReadUrl, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({})
                    })
                            .then(response => response.json())
                            .then(data => {
                                console.log("✅ Notification marked as read:", data);
                                // Update the UI to reflect that the notification is read
                                notificationRow.classList.remove("fw-bold");
                                notificationRow.classList.add("text-muted");
                                const icon = notificationRow.querySelector("i.fas");
                                if (icon) {
                                    icon.classList.remove("fa-envelope", "text-primary");
                                    icon.classList.add("fa-envelope-open", "text-secondary");
                                }
                            })
                            .catch(error => {
                                console.error("❌ Error marking notification as read:", error);
                            });
                }
            });
        });

        const deleteButtons = document.querySelectorAll(".notification-delete");
        deleteButtons.forEach(button => {
            button.addEventListener("click", function () {
                if (confirm("Are you sure you want to delete this notification?")) {
                    const notificationRow = this.closest(".notification-row");
                    notificationRow.remove();
                }
            });
        });
    });
</script>
@endsection
