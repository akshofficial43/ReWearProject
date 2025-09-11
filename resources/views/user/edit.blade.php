@extends('layouts.app')

@section('title', 'Edit Profile | ReWear')

@section('styles')
<style>
/* Edit Profile – Unique Page Styles (scoped with .epu) */
.epu-container { max-width: 1100px; margin: 24px auto; padding: 0 16px; }
.epu-header { background: linear-gradient(135deg, #23e5db 0%, #20b2aa 100%); color:#002f34; border-radius: 12px; padding: 28px 24px; position: relative; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
.epu-header h1 { margin: 0 0 6px 0; font-size: 28px; font-weight: 800; letter-spacing: 0.3px; }
.epu-header p { margin: 0; color: #0d4a4f; font-weight: 500; opacity: 0.9; }
.epu-grid { display: grid; grid-template-columns: 320px 1fr; gap: 18px; margin-top: 18px; }
.epu-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.06); padding: 18px; }

/* Avatar card */
.epu-avatar-wrap { position: relative; width: 140px; height: 140px; border-radius: 50%; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.12), inset 0 0 0 6px #e9eef0; background:#e9eef0; }
.epu-avatar { width: 100%; height: 100%; object-fit: cover; display:block; }
.epu-initial { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:52px; color:#406367; font-weight: 800; }
.epu-change { position:absolute; right: -6px; bottom: -6px; background:#002f34; color:#23e5db; border-radius: 999px; padding: 10px 12px; font-size: 13px; font-weight: 700; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); cursor: pointer; transition: transform .15s; }
.epu-change:hover { transform: translateY(-1px); }
.epu-side-meta { margin-top: 14px; }
.epu-side-meta h3 { margin: 0 0 4px; font-size: 18px; color:#002f34; }
.epu-side-meta .muted { color:#406367; font-size: 13px; }
.epu-side-actions { margin-top: 16px; display:flex; gap:10px; flex-wrap: wrap; }
.epu-danger { background:#F44336; color:#fff; border:none; padding:8px 12px; border-radius:8px; font-weight:600; cursor:pointer; }

/* Form styles alignment with project */
.epu-section { margin-bottom: 16px; }
.epu-section h4 { margin: 0 0 10px; font-size: 16px; color:#002f34; position: relative; }
.epu-section h4:after { content:""; display:block; width: 36px; height: 3px; background:#23e5db; border-radius: 2px; margin-top: 6px; }
.epu-row { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
.epu-row-3 { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; }
.epu-actions { display:flex; gap:10px; justify-content:flex-end; margin-top: 18px; }

/* Responsive */
@media (max-width: 992px) { .epu-grid { grid-template-columns: 1fr; } .epu-row-3 { grid-template-columns: 1fr; } .epu-row { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="epu-container">
<div class="epu-header">
<h1>Edit your profile</h1>
<p>Keep your information fresh so buyers can reach you easily.</p>

@if (session('success'))
<div class="alert success">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="alert error">{{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="alert error">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif
</div>

<div class="epu-grid">
<!-- Left: Avatar and quick actions -->
<aside class="epu-card">
<div class="epu-avatar-wrap">
@if($user->profile_image)
<img id="avatarPreview" class="epu-avatar" src="{{ asset('storage/'.$user->profile_image) }}" alt="{{ $user->name }}">
@else
<div class="epu-initial">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
<img id="avatarPreview" class="epu-avatar" src="" alt="" style="display:none;">
@endif
<label for="profile_image" class="epu-change"><i class="fas fa-camera"></i> Change</label>
</div>
<div class="epu-side-meta">
<h3>{{ $user->name }}</h3>
<div class="muted">{{ $user->email }}</div>
</div>
<div class="epu-side-actions">
<a href="{{ route('profile.password') }}" class="cancel-btn" style="text-decoration:none;">Change password</a>
@if($user->profile_image)
<button type="button" id="removeImageBtn" class="epu-danger"><i class="fas fa-trash"></i> Remove photo</button>
@endif
</div>
</aside>

<!-- Right: Form -->
<section class="epu-card">
@php
$addr = isset($user->addressData) && is_array($user->addressData) ? $user->addressData : [];
@endphp

<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="epu-section">
<h4>Personal info</h4>
<div class="epu-row">
<div class="form-group">
<label>Full Name</label>
<input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
</div>
<div class="form-group">
<label>Email</label>
<input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
</div>
</div>
</div>

<div class="epu-section">
<h4>Contact</h4>
<div class="epu-row">
<div class="form-group">
<label>Phone</label>
<input class="form-control" type="text" name="phone" value="{{ old('phone', $addr['phone'] ?? '') }}">
</div>
<div class="form-group">
<label>Street</label>
<input class="form-control" type="text" name="street" value="{{ old('street', $addr['street'] ?? '') }}">
</div>
</div>
</div>

<div class="epu-section">
<h4>Address</h4>
<div class="epu-row-3">
<div class="form-group">
<label>City</label>
<input class="form-control" type="text" name="city" value="{{ old('city', $addr['city'] ?? '') }}">
</div>
<div class="form-group">
<label>State</label>
<input class="form-control" type="text" name="state" value="{{ old('state', $addr['state'] ?? '') }}">
</div>
<div class="form-group">
<label>ZIP</label>
<input class="form-control" type="text" name="zip" value="{{ old('zip', $addr['zip'] ?? '') }}">
</div>
</div>
</div>

<input type="file" name="profile_image" id="profile_image" accept="image/*" class="file-input" onchange="previewAvatar(event)">

<div class="epu-actions">
<a href="{{ route('profile.show') }}" class="cancel-btn">Cancel</a>
<button type="submit" class="submit-btn">Save Changes</button>
</div>
</form>
</section>
</div>

@if($user->profile_image)
<form id="removeImageForm" action="{{ route('profile.image.remove') }}" method="POST" style="display:none;">
@csrf
</form>
@endif
</div>
@endsection

@section('scripts')
<script>
function previewAvatar(e){
	const file = e.target.files && e.target.files[0];
	const img = document.getElementById('avatarPreview');
	if(!file){
		if(img){ img.style.display = 'none'; img.src = ''; }
		return;
	}
	const reader = new FileReader();
	reader.onload = function(evt){
		if(img){
			img.src = evt.target.result;
			img.style.display = 'block';
		}
	};
	reader.readAsDataURL(file);
}

document.addEventListener('DOMContentLoaded', function(){
	const btn = document.getElementById('removeImageBtn');
	if(btn){
		btn.addEventListener('click', function(){
			if(confirm('Remove your profile image?')){
				document.getElementById('removeImageForm').submit();
			}
		});
	}
});
</script>
@endsection
