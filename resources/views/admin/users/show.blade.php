@extends('admin.layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">User #{{ $user->userId }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user->userId) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : asset('images/default-profile.png') }}" class="img-fluid rounded" alt="Profile">
                </div>
                <div class="col-md-9">
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-semibold">Name</div>
                        <div class="col-sm-9">{{ $user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-semibold">Email</div>
                        <div class="col-sm-9">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-semibold">Role</div>
                        <div class="col-sm-9"><span class="badge bg-secondary text-uppercase">{{ $user->role }}</span></div>
                    </div>
                    @if($user->address)
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-semibold">Address</div>
                        <div class="col-sm-9">{{ $user->address }}</div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-semibold">Joined</div>
                        <div class="col-sm-9">{{ $user->created_at?->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
