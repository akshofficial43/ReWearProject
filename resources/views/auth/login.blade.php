@extends('layouts.app')

@section('title', 'Login - ReWear')

@section('content')
<div class="auth-container">
    <div class="auth-box">
        <h1 class="auth-title">Login</h1>
        <p class="auth-subtitle">Access your ReWear account</p>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input">
                    <input id="password" type="password" name="password" required class="form-control @error('password') is-invalid @enderror">
                    <button type="button" class="toggle-password">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Remember me</label>
                </div>
            </div>
            
            <button type="submit" class="auth-submit">Login</button>
            
            <div class="auth-divider">
                <span>or</span>
            </div>
            
            <a href="#" class="social-login google">
                <i class="fab fa-google"></i>
                <span>Continue with Google</span>
            </a>
            
            <div class="auth-footer">
                Don't have an account? <a href="{{ route('register') }}" class="auth-link">Register</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const password = document.querySelector('#password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
@endsection