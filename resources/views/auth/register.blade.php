@extends('layouts.app')

@section('title', 'Register - ReWear')

@section('content')
<div class="auth-container">
    <div class="auth-box register-box">
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join ReWear to buy and sell pre-loved items</p>
        
        @if(session('otp_required'))
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('verify.otp') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="otp">Enter OTP sent to your email</label>
                    <input id="otp" type="text" name="otp" required class="form-control">
                </div>
                <input type="hidden" name="email" value="{{ session('email') }}">
                <button type="submit" class="auth-submit">Verify OTP</button>
            </form>
                @php
                    $user = \App\Models\User::where('email', session('email'))->first();
                @endphp
               
                @if($errors->has('otp'))
                    <div class="alert alert-danger">
                        {{ $errors->first('otp') }}
                    </div>
                @endif

        @else
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form method="POST" action="{{ route('register') }}" class="auth-form" enctype="multipart/form-data">
                @csrf
                <!-- ...existing registration form code... -->
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="profile_image">Profile Photo</label>
                    <div class="custom-file-upload">
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" class="file-input @error('profile_image') is-invalid @enderror">
                        <label for="profile_image" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Choose a photo</span>
                        </label>
                        <div class="file-preview"></div>
                    </div>
                    <small class="form-text text-muted">Optional. Max size: 1MB</small>
                    @error('profile_image')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <div class="password-input">
                        <input id="password" type="password" name="password" required class="form-control @error('password') is-invalid @enderror">
                        <button type="button" class="toggle-password" data-target="password">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">Must be at least 8 characters long</small>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                    <div class="password-input">
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
                        <button type="button" class="toggle-password" data-target="password_confirmation">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group terms">
                    <div class="checkbox-container">
                        <input type="checkbox" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
                        <label for="terms">I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></label>
                    </div>
                    @error('terms')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="auth-submit">Register</button>
                <div class="auth-divider">
                    <span>or</span>
                </div>
                <a href="#" class="social-login google">
                    <i class="fab fa-google"></i>
                    <span>Continue with Google</span>
                </a>
                <div class="auth-footer">
                    Already have an account? <a href="{{ route('login') }}" class="auth-link">Login</a>
                </div>
            </form>
        @endif
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
    
    // File upload preview
    const fileInput = document.getElementById('profile_image');
    const filePreview = document.querySelector('.file-preview');
    const fileLabel = document.querySelector('.file-label span');
    
    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                filePreview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
                filePreview.style.display = 'block';
                fileLabel.textContent = file.name;
            };
            reader.readAsDataURL(file);
        } else {
            filePreview.innerHTML = '';
            filePreview.style.display = 'none';
            fileLabel.textContent = 'Choose a photo';
        }
    });
</script>
@endsection