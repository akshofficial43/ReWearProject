@extends('layouts.app')

@section('title', Auth::user()->name . ' - Profile | ReWear')

@section('content')
<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-cover"></div>
        <div class="profile-user">
            <div class="profile-avatar">
                @if(Auth::user()->profile_image)
                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}">
                @else
                    <div class="avatar-placeholder">
                        <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h1>{{ Auth::user()->name }}</h1>
                <p class="member-since">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Tab Navigation -->
        <div class="profile-tabs">
            <div class="tab active">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </div>
            <a href="{{ route('products.index', ['user' => Auth::id()]) }}" class="tab">
                <i class="fas fa-tag"></i>
                <span>My Listings</span>
            </a>
            <div class="tab">
                <i class="fas fa-heart"></i>
                <span>Favorites</span>
            </div>
            <a href="{{ route('profile.edit') }}" class="tab">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>

        <!-- Profile Details -->
        <div class="profile-details">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="section">
                <div class="section-header">
                    <h2>Personal Information</h2>
                    <a href="{{ route('profile.edit') }}" class="edit-button">
                        <i class="fas fa-pencil-alt"></i> Edit
                    </a>
                </div>
                <div class="details-card">
                    @php
                        $addressData = [];
                        if (!empty(Auth::user()->address)) {
                            try {
                                $addressData = json_decode(Auth::user()->address, true) ?: [];
                            } catch (\Exception $e) {
                                $addressData = [];
                            }
                        }
                    @endphp

                    <div class="details-grid">
                        <div class="detail-item">
                            <label>Full Name</label>
                            <p>{{ Auth::user()->name }}</p>
                        </div>
                        
                        <div class="detail-item">
                            <label>Email Address</label>
                            <p>{{ Auth::user()->email }}</p>
                        </div>
                        
                        <div class="detail-item">
                            <label>Phone Number</label>
                            <p>{{ $addressData['phone'] ?? 'Not provided' }}</p>
                        </div>
                        
                        <div class="detail-item">
                            <label>Location</label>
                            <p>{{ $addressData['city'] ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Recent Listings -->
            <div class="section">
                <div class="section-header">
                    <h2>My Listings</h2>
                    <a href="{{ route('products.index', ['user' => Auth::id()]) }}" class="view-all">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="listings-grid">
                    @php
                        $hasListings = false;
                        if(method_exists(Auth::user(), 'products')) {
                            try {
                                $products = Auth::user()->products()->latest()->take(4)->get();
                                $hasListings = $products->count() > 0;
                            } catch (\Exception $e) {
                                $hasListings = false;
                            }
                        }
                    @endphp

                    @if($hasListings)
                        @foreach($products as $product)
                        <div class="listing-card">
                            <a href="{{ route('products.show', $product->productId) }}">
                                <div class="listing-image">
                                    @if(isset($product->images) && $product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="no-image">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                    <span class="listing-status {{ $product->status ?? 'available' }}">
                                        {{ ucfirst($product->status ?? 'Available') }}
                                    </span>
                                </div>
                                <div class="listing-details">
                                    <h3 class="listing-price">₹ {{ number_format($product->price, 0) }}</h3>
                                    <p class="listing-title">{{ $product->name }}</p>
                                    <div class="listing-meta">
                                        <span><i class="fas fa-map-marker-alt"></i> {{ $addressData['city'] ?? 'Delhi' }}</span>
                                        <span><i class="fas fa-clock"></i> {{ $product->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                            <div class="listing-actions">
                                <a href="{{ route('products.edit', $product->productId) }}" class="action-edit">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </a>
                                <form action="{{ route('products.destroy', $product->productId) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-delete" onclick="return confirm('Are you sure you want to delete this listing?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="no-listings">
                            <div class="no-data-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <h3>No listings yet</h3>
                            <p>Start selling your pre-loved items today</p>
                            <a href="{{ route('products.create') }}" class="btn-sell">+ SELL</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Stats -->
            <div class="section">
                <div class="section-header">
                    <h2>Account Activity</h2>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="stat-info">
                            @php
                                $listingCount = 0;
                                if(method_exists(Auth::user(), 'products')) {
                                    try {
                                        $listingCount = Auth::user()->products()->count();
                                    } catch (\Exception $e) {
                                        $listingCount = 0;
                                    }
                                }
                            @endphp
                            <h3>{{ $listingCount }}</h3>
                            <p>Active Listings</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Profile Views</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="stat-info">
                            @php
                                $messageCount = 0;
                                try {
                                    // Use the correct column names (snake_case)
                                    $sentCount = \App\Models\Message::where('sender_id', Auth::id())->count();
                                    $receivedCount = \App\Models\Message::where('receiver_id', Auth::id())->count();
                                    $messageCount = $sentCount + $receivedCount;
                                } catch (\Exception $e) {
                                    $messageCount = 0;
                                }
                            @endphp
                            <h3>{{ $messageCount }}</h3>
                            <p>Messages</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Favorites</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
/* Profile Styles */
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.profile-header {
    position: relative;
    margin-bottom: 30px;
}

.profile-cover {
    height: 180px;
    background-color: #e9fff9;
    border-radius: 12px;
}

.profile-user {
    display: flex;
    align-items: center;
    margin-top: -40px;
    padding: 0 20px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: white;
    overflow: hidden;
    border: 4px solid white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #00e5cc;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: bold;
}

.profile-info {
    margin-left: 20px;
}

.profile-info h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 0 0 5px 0;
    color: #002f34;
}

.member-since {
    color: #777;
    margin: 0;
}

.profile-content {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
}

.profile-tabs {
    display: flex;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 30px;
    width: 100%;
}

.tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px 25px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
}

.tab i {
    font-size: 22px;
    margin-bottom: 8px;
    color: #555;
}

.tab span {
    font-size: 14px;
    color: #555;
}

.tab.active {
    background-color: #00e5cc;
}

.tab.active i, .tab.active span {
    color: #002f34;
    font-weight: bold;
}

.tab:hover:not(.active) {
    background-color: #f5f5f5;
}

.profile-details {
    width: 100%;
}

.section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    padding: 20px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}

.section-header h2 {
    font-size: 18px;
    font-weight: bold;
    color: #002f34;
    margin: 0;
}

.edit-button {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #00e5cc;
    font-weight: 500;
    font-size: 14px;
}

.edit-button:hover {
    text-decoration: underline;
}

.view-all {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #00e5cc;
    font-size: 14px;
    font-weight: 500;
}

.view-all i {
    font-size: 12px;
}

.view-all:hover {
    text-decoration: underline;
}

.details-card {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.detail-item label {
    display: block;
    font-size: 12px;
    color: #777;
    margin-bottom: 5px;
}

.detail-item p {
    font-size: 16px;
    margin: 0;
    color: #002f34;
}

.listings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.listing-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.listing-card:hover {
    transform: translateY(-3px);
}

.listing-card a {
    text-decoration: none;
    color: inherit;
}

.listing-image {
    position: relative;
    height: 180px;
    background-color: #f0f0f0;
}

.listing-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
}

.no-image i {
    font-size: 48px;
    color: #ccc;
}

.listing-status {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-transform: capitalize;
}

.listing-status.available {
    background-color: #00e5cc;
    color: #002f34;
}

.listing-status.sold {
    background-color: #ff5252;
    color: white;
}

.listing-details {
    padding: 12px;
}

.listing-price {
    font-size: 18px;
    font-weight: bold;
    margin: 0 0 5px 0;
    color: #002f34;
}

.listing-title {
    font-size: 14px;
    margin: 0 0 5px 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.listing-meta {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #777;
}

.listing-meta i {
    margin-right: 4px;
}

.listing-actions {
    display: flex;
    border-top: 1px solid #eee;
}

.listing-actions a, .listing-actions button {
    flex: 1;
    padding: 10px;
    text-align: center;
    background: none;
    border: none;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.2s;
}

.action-edit {
    color: #00e5cc;
}

.action-delete {
    color: #ff5252;
}

.listing-actions a:hover, .listing-actions button:hover {
    background: #f9f9f9;
}

.no-listings {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px 0;
}

.no-data-icon {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 80px;
    height: 80px;
    background-color: #f0f0f0;
    border-radius: 50%;
    margin-bottom: 15px;
}

.no-data-icon i {
    font-size: 32px;
    color: #999;
}

.no-listings h3 {
    font-size: 18px;
    font-weight: bold;
    margin: 0 0 5px 0;
    color: #002f34;
}

.no-listings p {
    color: #777;
    margin: 0 0 15px 0;
}

.btn-sell {
    display: inline-block;
    background-color: #00e5cc;
    color: #002f34;
    font-weight: bold;
    padding: 10px 24px;
    border-radius: 4px;
    text-decoration: none;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.stat-card {
    display: flex;
    align-items: center;
    background: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
}

.stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: #e9fff9;
    border-radius: 50%;
    margin-right: 15px;
}

.stat-icon i {
    font-size: 20px;
    color: #00e5cc;
}

.stat-info h3 {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
    color: #002f34;
}

.stat-info p {
    font-size: 12px;
    color: #777;
    margin: 5px 0 0 0;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.alert-success {
    background-color: #e9fff9;
    color: #00a88f;
    border: 1px solid #00e5cc;
}

/* Responsive styles */
@media (max-width: 992px) {
    .details-grid, .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .profile-user {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-info {
        margin: 15px 0 0 0;
    }
    
    .tab {
        padding: 15px 10px;
    }
    
    .listings-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}

@media (max-width: 576px) {
    .details-grid, .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .listings-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection