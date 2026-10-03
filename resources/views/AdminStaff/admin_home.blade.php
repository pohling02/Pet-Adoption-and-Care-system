@extends('layouts.admin_master')
@section('title', 'Admin Dashboard')
@section('content')
<div class="container py-4">
    <!-- Admin Header Card -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-auto d-none d-md-block">
                    <div class="bg-primary text-white rounded-circle p-3">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                </div>
                <div class="col">
                    <h2 class="fw-bold text-primary mb-1">Welcome, Admin!</h2>
                    <p class="text-muted mb-0">Manage users and restrict access to the platform.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Actions Card -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0 fw-bold text-primary">Management Console</h5>
        </div>
        <div class="card-body p-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <a href="{{ route('admin.manage_users') }}" class="card h-100 border-0 shadow-sm hover-shadow text-decoration-none">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-primary"></i>
                            </div>
                            <h4 class="fw-bold">Manage Users</h4>
                            <p class="text-muted mb-0">View, edit, and manage user accounts</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all .3s ease;
    }
</style>
@endsection