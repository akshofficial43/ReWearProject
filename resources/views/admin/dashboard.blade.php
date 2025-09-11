@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Dashboard</h1>
        <div>
            <span class="text-muted">Last updated: {{ now()->format('M d, Y H:i') }}</span>
            <button class="btn btn-sm btn-light ms-2">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card stat-card-primary">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $totalUsers ?? '0' }}</h3>
                        <p class="mb-0">Total Users</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-white-50">
                        <i class="fas fa-arrow-up"></i> {{ $newUsers ?? '0' }} new this week
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card stat-card-info">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $totalProducts ?? '0' }}</h3>
                        <p class="mb-0">Products</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-white-50">
                        <i class="fas fa-arrow-up"></i> {{ $newProducts ?? '0' }} new this week
                    </span>
                </div>
            </div>
        </div>
    <div class="col-lg-3 col-md-6">
            <div class="card stat-card stat-card-success">
                <div class="d-flex justify-content-between">
                    <div>
            <h3 class="mb-0 fw-bold">₹{{ number_format(($totalRevenue ?? 0), 2) }}</h3>
                        <p class="mb-0">Revenue</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-white-50">
            <i class="fas fa-arrow-up"></i> {{ $salesGrowth ?? '—' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card stat-card-warning">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $pendingOrders ?? '0' }}</h3>
                        <p class="mb-0">Orders Pending</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-white-50">
                        <i class="fas fa-hourglass-half"></i> Needs attention
                    </span>
                </div>
            </div>
        </div>
</div>

   

    </div>
</div>
@endsection


