<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Admin Panel')</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="icon" type="image/png" href="{{ asset('images/logo2.png') }}">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('admin.home') }}">Admin Panel</a>
                <div class="collapse navbar-collapse justify-content-end">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.manage_users') ? 'active' : '' }}" href="{{ route('admin.manage_users') }}">Manage Users</a>
                        </li>
                        <!--                <li class="nav-item">
                                            <a class="nav-link {{ Request::routeIs('admin.shelter_requests') ? 'active' : '' }}" href="{{ route('admin.shelter_requests') }}">Shelter Approvals</a>
                                        </li>-->
                        <li class="nav-item">
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            @yield('content')
        </div>

        <!-- ✅ Bootstrap JS Bundle (required for modals and tooltips) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
