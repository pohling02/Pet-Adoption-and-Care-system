<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Petopia - Adopter')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

        @stack('styles')
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .navbar {
                background-color: #343a40 !important;
                padding: 0 20px;
                border-bottom: 1px solid #23272b;
                height: 70px; /* Fixed height */
                display: flex;
                align-items: center;
            }

            .navbar-brand {
                font-size: 1.5rem;
                font-weight: bold;
                color: #FFFFFF !important;
                display: flex;
                align-items: center;
                margin-right: 20px;
            }

            .nav-link {
                color: #FFFFFF !important;
                font-size: 14px;
                font-weight: 500;
                text-align: center;
                transition: all 0.3s ease;
                padding: 8px 15px !important;
                margin: 0 2px;
                border-radius: 4px;
                height: 100%;
                display: flex;
                align-items: center;
            }

            .nav-link.active, .nav-link:hover {
                background-color: #1A4E8C !important;
                color: #FFFFFF !important;
            }

            .navbar-nav {
                display: flex;
                align-items: center;
                height: 100%;
            }

            .navbar-nav .nav-item {
                display: flex;
                align-items: center;
                height: 100%;
            }

            .message-icon {
                position: relative;
                display: inline-block;
            }

            .navbar-nav .message-icon i,
            .navbar-nav .notification-icon i {
                font-size: 22px;
                color: #FFFFFF;
            }

            .message-icon .badge {
                position: absolute;
                top: -8px;
                right: -8px;
                background-color: red;
                color: white;
                font-size: 12px;
                padding: 4px 7px;
                border-radius: 50%;
                line-height: 1;
                min-width: 18px;
                text-align: center;
            }

            .icon-link {
                position: relative;
                display: flex;
                align-items: center;
                padding: 8px 12px !important;
                height: 100%;
            }

            .icon-link i {
                font-size: 22px;
                color: #FFFFFF;
                vertical-align: middle;
            }

            .icon-link .badge {
                position: absolute;
                top: 0;
                right: 0;
                background-color: red;
                color: white;
                font-size: 12px;
                padding: 4px 7px;
                border-radius: 50%;
                line-height: 1;
                min-width: 18px;
                text-align: center;
            }

            @keyframes blink {
                50% {
                    opacity: 0;
                }
            }

            .blinking-badge {
                animation: blink 1s infinite;
            }

            .main-content {
                flex: 1;
                min-height: 75vh;
            }

            footer {
                background-color: #343a40;
                color: white;
                padding: 20px 0;
                margin-top: 30px;
            }

            /* Improved profile dropdown */
            .nav-item.dropdown {
                display: flex;
                align-items: center;
            }

            .nav-item.dropdown .nav-link {
                display: flex;
                align-items: center;
            }

            /* Make sure all navbar items are consistent height */
            .navbar-collapse {
                height: 100%;
            }

            .dropdown-menu {
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <!-- Unread Message Alert -->
        @if(Auth::check())
        @php
        $unreadMessages = \App\Models\Message::where('ReceiverID', Auth::id())->where('is_read', false)->count();
        @endphp

        @if($unreadMessages > 0)
        <div class="alert alert-warning text-center" role="alert">
            <i class="fas fa-envelope"></i> You have <strong>{{ $unreadMessages }}</strong> unread message{{ $unreadMessages > 1 ? 's' : '' }}.
            <a href="{{ route('messages.list') }}" class="text-dark fw-bold">Click here to check.</a>
        </div>
        @endif
        @endif
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ route('adopter.home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Petopia Logo" class="logo" style="height: 45px; width: 45px; border-radius: 50%; object-fit: cover;">
                    <span class="ms-2">PETOPIA</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('adopter.home') ? 'active' : '' }}" href="{{ route('adopter.home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('adopter.petlist') ? 'active' : '' }}" href="{{ route('adopter.petlist') }}">Adopt A Pet</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('appointments.create') ? 'active' : '' }}" href="{{ route('appointments.create') }}">Appointment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('adopter.resources') ? 'active' : '' }}" href="{{ route('adopter.resources') }}">Pet Care Resources</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                        </li>
                        
                        <!-- Profile Dropdown -->
                        <li class="nav-item dropdown ms-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                @if(Auth::check())
                                @php
                                $user = Auth::user()->load('adopterProfile');
                                $profile = $user->adopterProfile;
                                $profilePicturePath = ($profile && !empty($profile->profile_picture) && file_exists(public_path($profile->profile_picture)))
                                ? asset($profile->profile_picture)
                                : asset('images/blankprofile.jpg');

                                @endphp

                                <img src="{{ $profilePicturePath }}" class="rounded-circle border border-secondary" style="height: 30px; width: 30px; object-fit: cover;">
                                <span class="ms-2">{{ Auth::user()->name }}</span>
                                @else
                                <img src="{{ asset('images/blankprofile.jpg') }}" class="rounded-circle" style="height: 30px; width: 30px; object-fit: cover;">
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(Auth::check())
                                <li><a class="dropdown-item" href="{{ route('adopter.profile.view') }}">My Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('adopter.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                                @else
                                <li><a class="dropdown-item" href="{{ route('adopter.login') }}">Login</a></li>
                                <li><a class="dropdown-item" href="{{ route('adopter.register.form') }}">Register</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link icon-link" href="{{ route('messages.list') }}">
                                <i class="fas fa-comment-alt fa-lg"></i> 
                                @if(isset($unreadMessages) && $unreadMessages > 0)
                                <span class="badge bg-danger blinking-badge">{{ $unreadMessages }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link icon-link" href="{{ route('adopter.notifications') }}">
                                <i class="fas fa-bell fa-lg"></i> 
                                @php
                                $unreadNotifications = Auth::check() ? \App\Models\Notification::where('UserID', Auth::id())->whereNull('read_at')->count() : 0;
                                @endphp
                                @if($unreadNotifications > 0)
                                <span class="badge bg-danger blinking-badge">
                                    {{ $unreadNotifications }}
                                </span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container-fluid mt-4 main-content">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="bg-dark text-white pt-4 mt-5">
            <div class="container">
                <div class="row">
                    <!-- Company Info -->
                    <div class="col-md-3">
                        <h5 class="fw-bold text-uppercase">Petopia Sdn.Bhd</h5>
                        <p>Your trusted pet adoption and care platform.</p>
                    </div>

                    <!-- Products/Services -->
                    <div class="col-md-3">
                        <h5 class="fw-bold text-uppercase">Products</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('adopter.petlist') }}" class="text-white text-decoration-none">Pet Adoption</a></li>
                            <li><a href="{{ route('adopter.resources') }}" class="text-white text-decoration-none">Pet Care Resources</a></li>
                            <li><a href="{{ route('adopter.pets.profile') }}" class="text-white text-decoration-none">Health Management</a></li>
                        </ul>
                    </div>

                    <!-- Useful Links -->
                    <div class="col-md-3">
                        <h5 class="fw-bold text-uppercase">Useful Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('faq') }}" class="text-white text-decoration-none">FAQ</a></li>
                            <li><a href="{{ route('privacy.policy') }}" class="text-white text-decoration-none">Privacy Policy</a></li>
                            <li><a href="{{ route('terms.service') }}" class="text-white text-decoration-none">Terms of Service</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-md-3">
                        <h5 class="fw-bold text-uppercase">Contact</h5>
                        <p><i class="bi bi-geo-alt"></i> Ground Floor, Block A, Setapak, Kuala Lumpur</p>
                        <p><i class="bi bi-envelope"></i> <a href="mailto:ngpl-wm22@student.tarc.edu.my" class="text-white text-decoration-none">ngpl-wm22@student.tarc.edu.my</a></p>
                        <p><i class="bi bi-telephone"></i> 019 456 7890</p>
                    </div>
                </div>

                <hr class="text-white">

                <!-- Copyright & Social Media -->
                <div class="text-center">
                    <p>© 2025 Copyright: Petopia</p>
                    <div>
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-google"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
        @yield('scripts')
    </body>
</html>