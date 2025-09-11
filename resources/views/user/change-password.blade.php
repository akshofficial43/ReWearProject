@extends('layouts.app')

@section('title', 'Change Password | ReWear')

@section('content')
<div class="profile-page">
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-cover"></div>
            <div class="profile-avatar-container">
                <div class="profile-avatar">
                    @if(Auth::user()->profile_image)
                        <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}">
                    @else
                        <div class="avatar-placeholder">
                            <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <h1 class="profile-name">{{ Auth::user()->name }}</h1>
                <p class="profile-since">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
            </div>
        </div>
        
        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Profile Navigation -->
            <div class="profile-nav">
                <ul class="profile-nav-list">
                    <li class="profile-nav-item">
                        <a href="{{ route('profile.show') }}">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="profile-nav-item">
                        <a href="{{ route('products.index', ['user' => Auth::id()]) }}">
                            <i class="fas fa-tag"></i>
                            <span>My Ads</span>
                        </a>
                    </li>
                    <li class="profile-nav-item active">
                        <a href="{{ route('profile.edit') }}">
                            <i class="fas fa-cog"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>
                    <li class="profile-nav-item">
                        <a href="{{ route('messages.index') }}">
                            <i class="fas fa-comment-alt"></i>
                            <span>Messages</span>
                        </a>
                    </li>
                    <li class="profile-nav-item">
                        <a href="{{ route('orders.index') }}">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="profile-nav-item">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
            
            <!-- Change Password -->
            <div class="profile-details">
                <div class="profile-section">
                    <div class="section-header">
                        <h2>Change Password</h2>
                    </div>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.password') }}" method="POST" class="change-password-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="password-input">
                                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                <button type="button" class="toggle-password" data-target="current_password">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <div class="password-input">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                <button type="button" class="toggle-password" data-target="password">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="form-tip">Must be at least 8 characters long</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <div class="password-input">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                <button type="button" class="toggle-password" data-target="password_confirmation">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Update Password</button>
                            <a href="{{ route('profile.edit') }}" class="cancel-btn">Back to Profile</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
</script>
@endsection