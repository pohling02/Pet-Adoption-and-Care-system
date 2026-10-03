<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Petopia - Shelter Staff')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">
        <style>
            :root {
                --petopia-blue: #1a4b8e;
                --petopia-blue-light: #2c5faa;
                --petopia-blue-dark: #133a72;
                --petopia-text: #333333;
                --petopia-gray-light: #f8f9fa;
                --petopia-gray: #e9ecef;
                --petopia-yellow: #ffc107;
            }

            body {
                font-family: 'Nunito', 'Segoe UI', sans-serif;
                color: var(--petopia-text);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .navbar {
                padding: 0;
                background-color: var(--petopia-blue) !important;
            }

            .navbar-nav {
                gap: 10px;
                display: flex;
                align-items: center;
            }

            .navbar-brand {
                color: white !important;
                padding: 15px;
            }

            .navbar-brand img {
                border-radius: 50%;
                height: 40px;
                width: 40px;
                object-fit: cover;
            }

            .navbar-brand .text-logo {
                color: white !important;
                font-weight: bold;
            }

            .nav-item .nav-link {
                color: white !important;
                font-weight: 500;
                margin: 0;
                padding: 20px 12px;
                transition: all 0.2s ease;
                border-bottom: 3px solid transparent;
            }

            .nav-link.active {
                background-color: var(--petopia-blue-dark);
                border-bottom: 3px solid white;
                position: relative;
            }

            .nav-link.active:after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 3px;
                background-color: white;
            }

            .nav-link:hover:not(.active) {
                background-color: var(--petopia-blue-light);
            }

            .nav-icon {
                font-size: 1.1rem;
                margin-right: 5px;
            }

            .btn-primary {
                background-color: var(--petopia-blue);
                border-color: var(--petopia-blue);
                padding: 8px 24px;
                font-weight: 500;
                transition: all 0.2s ease;
            }

            .btn-primary:hover, .btn-primary:focus {
                background-color: var(--petopia-blue-dark);
                border-color: var(--petopia-blue-dark);
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .btn-outline-primary {
                color: var(--petopia-blue);
                border-color: var(--petopia-blue);
            }

            .btn-outline-primary:hover {
                background-color: var(--petopia-blue);
                border-color: var(--petopia-blue);
            }

            .container-fluid {
                padding-left: 15px;
                padding-right: 15px;
                max-width: 1400px;
                margin: 0 auto;
            }

            .dashboard-card {
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                height: 100%;
                border: none;
            }

            .dashboard-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            }

            .dashboard-card .btn-primary {
                padding: 8px 24px;
                font-weight: 500;
                transition: all 0.2s ease;
            }

            .dashboard-card .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .card-icon {
                font-size: 2.5rem;
                color: var(--petopia-blue);
                margin-bottom: 1rem;
            }

            .announcements-icon {
                color: var(--petopia-yellow);
            }

            .footer {
                margin-top: auto;
                background-color: var(--petopia-gray-light);
                color: var(--petopia-text);
            }

            .menu-toggle {
                cursor: pointer;
            }

            .dropdown-menu {
                background-color: var(--petopia-blue) !important; /* Match navbar color */
                border: none;
                border-radius: 8px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
                min-width: 220px; /* Make sure it has a good width */
                padding: 8px 0;
            }

            .dropdown-item {
                color: white !important;
                font-size: 14px;
                font-weight: 500;
                padding: 10px 15px;
                transition: background 0.2s ease-in-out, padding 0.2s ease;
            }

            .dropdown-item:hover {
                background-color: var(--petopia-blue-light) !important;
                color: white !important;
                padding-left: 18px;
            }

            .dropdown-item.active {
                background-color: var(--petopia-blue-dark) !important;
                font-weight: 600;
            }

            .dropdown-divider {
                background-color: rgba(255, 255, 255, 0.2);
            }

            .navbar .dropdown-toggle::after {
                margin-left: 6px;
                vertical-align: middle;
                border-top: 5px solid white;
            }

            .dropdown-item.disabled {
                color: #b5b5b5 !important;
                pointer-events: none;
            }

            .dashboard-header {
                background-color: var(--petopia-gray-light);
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            @media (min-width: 993px) {
                .navbar-nav {
                    gap: 5px;
                }
            }

            @media (max-width: 992px) {
                .navbar-expand-lg .navbar-nav {
                    gap: 5px;
                }

                .navbar-nav.ms-auto {
                    margin-right: 15px;
                }

                .hide-on-mobile {
                    display: none;
                }

                .nav-icon {
                    font-size: 1.4rem;
                    margin-right: 0;
                }

                .navbar-nav {
                    flex-direction: row;
                    justify-content: space-between;
                    gap:10px;
                }

                .nav-item {
                    flex: 1;
                    text-align: center;
                }

                .nav-link {
                    padding: 15px 8px !important;
                }
            }
        </style>

        <link rel="stylesheet" href="{{ asset('css/shelter_staff.css') }}">
        @stack('styles') 
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <!-- Navigation Bar - Redesigned -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('shelter.home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Petopia Logo" height="40">
                    <span class="ms-2 text-logo">PETOPIA</span>
                </a>

                <!-- Mobile Menu Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Main Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <!-- Primary Navigation Items -->
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('shelter.home') ? 'active' : '' }}" href="{{ route('shelter.home') }}">
                                <i class="bi bi-house-door nav-icon"></i>
                                <span class="hide-on-mobile">Home</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('shelter.pets.manage') ? 'active' : '' }}" href="{{ route('shelter.pets.manage') }}">
                                <i class="bi bi-clipboard-check nav-icon"></i>
                                <span class="hide-on-mobile">Manage Pets</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('shelter.adoptions') ? 'active' : '' }}" href="{{ route('shelter.adoptions') }}">
                                <i class="bi bi-heart nav-icon"></i>
                                <span class="hide-on-mobile">Adoption Requests</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('shelter.health') ? 'active' : '' }}" href="{{ route('shelter.health') }}">
                                <i class="bi bi-file-medical nav-icon"></i>
                                <span class="hide-on-mobile">Pet Health</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('shelter.appointments') ? 'active' : '' }}" href="{{ route('shelter.appointments') }}">
                                <i class="bi bi-calendar-check nav-icon"></i>
                                <span class="hide-on-mobile">Appointments</span>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots nav-icon"></i> <span class="hide-on-mobile">More</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('messages.index') }}">
                                        <i class="bi bi-chat-dots me-2"></i> Message Center
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('shelter.notifications') }}">
                                        <i class="bi bi-megaphone me-2"></i> Notification
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('shelter.resources.manage') }}">
                                        <i class="bi bi-journal-text me-2"></i> Pet Care Resources
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>

                    <!-- Profile Area (pushed to the right) -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                @if(Auth::check())
                                @php
                                $user = Auth::user()->load('shelterStaffProfile');
                                $profilePicturePath = optional($user->shelterStaffProfile)->profile_picture 
                                ? asset($user->shelterStaffProfile->profile_picture) 
                                : asset('images/blankprofile.jpg');
                                @endphp
                                <img src="{{ $profilePicturePath }}" class="rounded-circle" style="height: 30px; width: 30px; object-fit: cover;">
                                <span class="ms-2 hide-on-mobile">{{ $user->name }}</span>
                                @else
                                <img src="{{ asset('images/blankprofile.jpg') }}" class="rounded-circle" style="height: 30px; width: 30px; object-fit: cover;">
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                                @if(Auth::check())
                                <li><a class="dropdown-item" href="{{ route('shelter.profile.view') }}"><i class="bi bi-person me-2"></i>My Profile</a></li>
                                <li><hr class="dropdown-divider bg-light opacity-25"></li>
                                <li>
                                    <form method="POST" action="{{ route('shelter_staff.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                                @else
                                <li><a class="dropdown-item" href="{{ route('shelter.login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a></li>
                                <li><a class="dropdown-item" href="{{ route('shelter.register.form') }}"><i class="bi bi-person-plus me-2"></i>Register</a></li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mt-4">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="footer text-center py-3 mt-5">
            <div class="container">
                <p class="mb-1">© 2025 Petopia. All rights reserved.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-secondary"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-secondary"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-secondary"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </footer>

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Additional Scripts -->
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>
        @stack('scripts')
        @yield('scripts')
    </body>
</html>