@extends('admin.layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Admin Profile</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    // Define default avatar color options and initials once
    $colorOptions = $colorOptions ?? ['#18d6c7','#00837b','#4f46e5','#0ea5e9','#06b6d4','#22c55e','#f59e0b','#ef4444','#a855f7','#14b8a6'];
    $nm = trim($admin->name ?? '');
    $parts = preg_split('/\s+/', $nm);
    $initials = '';
    if (is_array($parts)) {
      foreach ($parts as $p) {
        if ($p !== '') { $initials .= strtoupper(function_exists('mb_substr') ? mb_substr($p, 0, 1) : substr($p, 0, 1)); }
        if (strlen($initials) >= 2) break;
      }
    }
    if ($initials === '') { $initials = 'A'; }
  @endphp

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-12">
          <label class="form-label">Avatar</label>
          <div class="d-flex flex-wrap align-items-center gap-3">
            <!-- Current/Preview -->
            <div class="avatar-preview rounded-circle overflow-hidden" id="avatar-preview" title="Current Avatar">
              @php($hasAvatar = (bool)$admin->profile_image)
              @if($hasAvatar)
                <img src="{{ asset('storage/' . $admin->profile_image) }}" alt="Avatar">
              @else
                <span class="placeholder"><i class="fa fa-user"></i></span>
              @endif
            </div>

            <!-- Upload -->
            <div class="flex-grow-1" style="min-width:260px;max-width:420px;">
              <label class="form-label small text-muted mb-1">Upload a photo</label>
              <input type="file" name="profile_image" id="profile_image" class="form-control form-control-sm @error('profile_image') is-invalid @enderror" accept="image/*">
              @error('profile_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">PNG, JPG up to 2MB. Square works best.</div>
            </div>
          </div>

          
        </div>

        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="form-control @error('name') is-invalid @enderror" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="form-control @error('email') is-invalid @enderror" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label class="form-label">New Password</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" placeholder="Leave blank to keep current">
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>

        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
.avatar-preview { width: 72px; height: 72px; background:#f1f5f9; display:inline-flex; align-items:center; justify-content:center; border:1px solid #e5e7eb; }
.avatar-preview img { width:100%; height:100%; object-fit:cover; }
.avatar-preview .placeholder { color:#94a3b8; font-size:22px; }
/* quick avatar chooser removed */
</style>
@endsection

@section('scripts')
<script>
  // Live preview on upload
  const fileInput = document.getElementById('profile_image');
  const previewImgBox = document.getElementById('avatar-preview');

  if (fileInput) {
    fileInput.addEventListener('change', (e) => {
      const [file] = e.target.files || [];
      if (!file) return;
      const url = URL.createObjectURL(file);
      previewImgBox.innerHTML = `<img src="${url}" alt="Avatar">`;
    });
  }
  // quick avatar chooser removed
</script>
@endsection
