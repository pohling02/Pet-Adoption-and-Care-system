@extends('layouts.adopter_master')

@section('title', 'Forgot Password')

@section('content')
<section class="bg-light min-vh-100 d-flex align-items-center justify-content-center py-3 py-md-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                <div class="card border border-light-subtle rounded-3 shadow-sm">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="text-center mb-3">
                            <a href="{{ route('adopter.home') }}">
                                <img src="{{ asset('images/logo.png') }}" alt="Petopia Logo" class="img-fluid" style="max-width: 150px;">
                            </a>
                        </div>
                        
                        <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Enter your email address to receive a password reset link.</h2>

                        @if(session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('adopter.password.email') }}">
                            @csrf
                            <div class="row gy-2 overflow-hidden">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                                        <label for="email" class="form-label">Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid my-3">
                                        <button class="btn btn-dark btn-lg" type="submit">Send Reset Link</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <a href="{{ route('adopter.login') }}" class="btn btn-outline-dark">Back to Login</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
