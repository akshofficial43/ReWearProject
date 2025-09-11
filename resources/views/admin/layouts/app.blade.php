<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - ReWear Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --admin-primary: #18d6c7;
            --admin-secondary: #00837b;
            --admin-dark: #004a45;
            --admin-light: #e9fbf9;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background-color: var(--admin-dark);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
        }
        
        .sidebar-brand {
            background-color: var(--admin-secondary);
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--admin-secondary);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
        }
        
        .btn-primary:hover {
            background-color: var(--admin-secondary);
            border-color: var(--admin-secondary);
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #555;
        }
        
        .stat-card {
            padding: 1.5rem;
            border-radius: 10px;
            color: white;
        }
        
        .stat-card-primary {
            background: linear-gradient(45deg, var(--admin-primary), var(--admin-secondary));
        }
        
        .stat-card-warning {
            background: linear-gradient(45deg, #ff9800, #ff5722);
        }
        
        .stat-card-success {
            background: linear-gradient(45deg, #4caf50, #2e7d32);
        }
        
        .stat-card-info {
            background: linear-gradient(45deg, #2196f3, #0d47a1);
        }
        
        .stat-icon {
            font-size: 3rem;
            opacity: 0.5;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding-left: 35px;
            border-radius: 20px;
        }
        
        .search-box i {
            position: absolute;
            left: 12px;
            top: 10px;
            color: #777;
        }
        
        .dropdown-item.active, .dropdown-item:active {
            background-color: var(--admin-primary);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand d-flex align-items-center">
            <i class="fas fa-recycle me-2"></i>
            <span>ReWear Admin</span>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    Products
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.official-products.index') }}" class="nav-link {{ request()->routeIs('admin.official-products.*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i>
                    Official Products
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    Orders
                </a>
            </li>
           
            <li class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </li>
            <li class="nav-item mt-4">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Site
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg mb-4">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="d-flex align-items-center ms-auto">
                    
                    <div class="dropdown">
                                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                                        @php($avatar = Auth::user()->profile_image ?? null)
                                                        @if($avatar)
                                                            <img src="{{ asset('storage/' . $avatar) }}" alt="Admin" class="rounded-circle" width="32" height="32">
                                                        @else
                                                            <span class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="fa fa-user text-secondary"></i></span>
                                                        @endif
                            <span class="ms-2">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('admin.profile.show') }}">Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    
    @yield('scripts')
</body>
</html>