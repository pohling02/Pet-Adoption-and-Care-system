@extends('layouts.shelter_master')
@section('title', 'Publish Announcement')
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
                        <i class="fa fa-bullhorn me-2"></i> Publish Announcement
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
                    
                    <!-- Create Announcement Form -->
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <form action="{{ route('shelter.notifications.store') }}" method="POST" class="border rounded p-4 mb-4 bg-light shadow-sm">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Recipients:</label>
                                    <select name="recipients" class="form-select">
                                        <option value="all_adopters">All Adopters</option>
                                        @foreach($adopters as $adopter)
                                            <option value="{{ $adopter->UserID }}">{{ $adopter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Subject:</label>
                                    <input type="text" class="form-control" name="subject" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Message:</label>
                                    <textarea class="form-control" name="message" rows="5" required></textarea>
                                    <div class="form-text">Be clear and concise in your message to ensure recipients understand the announcement.</div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" onclick="window.location='{{ route('shelter.notifications') }}'" class="btn btn-outline-secondary me-md-2">
                                        <i class="fa fa-times me-1"></i> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-paper-plane me-1"></i> Publish Announcement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- List of Announcements -->
                    <div class="mt-5">
                        <h3 class="border-bottom pb-2 mb-3">
                            <i class="fa fa-history me-2"></i> Past Announcements
                        </h3>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3">Subject</th>
                                        <th class="py-3">Message</th>
                                        <th class="py-3">Posted On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($announcements as $announcement)
                                        <tr>
                                            <td class="py-3 fw-bold">{{ strtok($announcement->message, '-') }}</td>
                                            <td class="py-3">{{ substr($announcement->message, strpos($announcement->message, '-') + 2) }}</td>
                                            <td class="py-3">{{ $announcement->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fa fa-bell-slash fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No previous announcements</p>
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
</div>
@endsection