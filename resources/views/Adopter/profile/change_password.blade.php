@extends('layouts.adopter_master')

@section('title', 'Change Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>🔑 Change Password</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form method="POST" action="{{ route('adopter.pw.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control" required>
                                <span class="input-group-text" id="toggleCurrentPassword" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                            @error('current_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- New Password Field with Toggle -->
                        <div class="form-group mt-3">
                            <label for="new_password">New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" required>
                                <span class="input-group-text" id="toggleNewPassword" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                            <div class="password-requirements mt-2">
                                <div class="alert alert-info py-2">
                                    <i class="bi bi-info-circle"></i> Password requirements:
                                    <ul class="mb-0 mt-1">
                                        <li>Minimum 8 characters</li>
                                        <li>At least one uppercase letter</li>
                                        <li>At least one lowercase letter</li>
                                        <li>At least one number</li>
                                        <li>At least one special character</li>
                                    </ul>
                                </div>
                            </div>
                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm New Password Field with Toggle -->
                        <div class="form-group mt-3">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                                <span class="input-group-text" id="toggleNewPasswordConfirmation" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Update Password</button>
                        <a href="{{ route('adopter.profile.view') }}" class="btn btn-secondary mt-3">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include Bootstrap Icons if not already loaded -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    // Toggle for Current Password field
    const toggleCurrentPassword = document.querySelector('#toggleCurrentPassword');
    const currentPasswordInput = document.querySelector('#current_password');

    toggleCurrentPassword.addEventListener('click', function () {
        const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        currentPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Toggle for New Password field
    const toggleNewPassword = document.querySelector('#toggleNewPassword');
    const newPasswordInput = document.querySelector('#new_password');

    toggleNewPassword.addEventListener('click', function () {
        const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        newPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Toggle for Confirm New Password field
    const toggleNewPasswordConfirmation = document.querySelector('#toggleNewPasswordConfirmation');
    const newPasswordConfirmationInput = document.querySelector('#new_password_confirmation');

    toggleNewPasswordConfirmation.addEventListener('click', function () {
        const type = newPasswordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
        newPasswordConfirmationInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });
</script>
@endsection