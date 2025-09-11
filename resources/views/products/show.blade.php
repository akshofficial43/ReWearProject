@extends('layouts.app')

@section('title', $product->name . ' - ReWear')

@section('content')
<div class="product-detail-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('products.index', ['category' => $product->category->categoryId]) }}">{{ $product->category->name }}</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ $product->name }}</span>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="product-detail-container">
            <!-- Left Column - Images -->
            <div class="product-images-column">
                <div class="product-image-gallery">
                    <div class="main-image">
                        @if($product->images->count() > 0)
                            <img id="current-image" src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>No image available</span>
                            </div>
                        @endif
                    </div>
                    
                    @if($product->images->count() > 1)
                    <div class="image-thumbnails">
                        @foreach($product->images as $image)
                            <div class="thumbnail {{ $loop->first ? 'active' : '' }}" data-src="{{ asset('storage/' . $image->image_path) }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <div class="product-description-box">
                    <h3>Description</h3>
                    <div class="description-content">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>
                
                <!-- Reviews Section -->
                <div class="product-reviews-box">
                    <div class="reviews-header">
                        <h3>Reviews</h3>
                        @auth
                            @if(Auth::id() != $product->userId)
                                <a href="#" class="write-review-btn">
                                    <i class="fas fa-star"></i>
                                    Write a review
                                </a>
                            @endif
                        @endauth
                    </div>
                    
                    @if(method_exists($product, 'reviews') && $product->reviews->count() > 0)
                        <div class="reviews-list">
                            @foreach($product->reviews as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="reviewer-avatar">
                                                @if($review->user->profile_image)
                                                    <img src="{{ asset('storage/' . $review->user->profile_image) }}" alt="{{ $review->user->name }}">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        <span>{{ substr($review->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="reviewer-details">
                                                <h4>{{ $review->user->name }}</h4>
                                                <span class="review-date">{{ $review->created_at->format('j M Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <p>{{ $review->comment }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-reviews">
                            <div class="no-reviews-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <p>No reviews yet for this product.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Right Column - Details -->
            <div class="product-details-column">
                <div class="product-info-box">
                    <div class="product-price">{{ number_format($product->price, 0) }}</div>
                    <h1 class="product-title">{{ $product->name }}</h1>
                    <div class="product-meta">
                        <span class="product-location">
                            {{ $product->location ? $product->location : 'Not specified' }}
                        </span>
                        <span class="product-time">{{ $product->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                
                <div class="seller-info-box">
                    <h3>Seller Details</h3>
                    <div class="seller-info">
                        <div class="seller-profile">
                            <div class="seller-avatar">
                                @if($product->user->profile_image)
                                    <img src="{{ asset('storage/' . $product->user->profile_image) }}" alt="{{ $product->user->name }}">
                                @else
                                    <div class="avatar-placeholder">
                                        <span>{{ substr($product->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="seller-details">
                                <h4>{{ $product->user->name }}</h4>
                                <p>Member since {{ $product->user->created_at->format('M Y') }}</p>
                                <a href="{{ route('profile.show', ['user' => $product->userId]) }}" class="see-profile">See profile</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="action-box">
                    @auth
                        @if(Auth::id() == $product->userId)
                            <a href="{{ route('products.edit', $product->productId) }}" class="edit-product-btn">
                                <i class="fas fa-edit"></i>
                                Edit Product
                            </a>
                        @elseif(!($product->user->role === 'admin' || $product->user->isAdmin()))
                            <a href="{{ url('/messages') }}?product_id={{ $product->productId }}&seller_id={{ $product->userId }}" class="chat-btn">
                                <i class="fas fa-comment-alt"></i>
                                Chat with Seller
                            </a>
                        @endif
                    @else
                        @if(!($product->user->role === 'admin' || $product->user->isAdmin()))
                            <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="chat-btn">
                                <i class="fas fa-comment-alt"></i>
                                Login to Chat
                            </a>
                        @endif
                    @endauth
                    
                    @if($product->user->role === 'admin' || $product->user->isAdmin())
                        <form action="{{ route('cart.add', $product->productId) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="cart-btn">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </form>
                    @endif
                    
                    <button class="favorite-btn" id="favorite-btn">
                        <i class="far fa-heart"></i>
                        Add to Favorites
                    </button>
                </div>
                
                <div class="product-details-box">
                    <h3>Details</h3>
                    <ul class="details-list">
                        <li>
                            <span class="detail-label">Condition</span>
                            <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $product->condition)) }}</span>
                        </li>
                        <li>
                            <span class="detail-label">Category</span>
                            <span class="detail-value">{{ $product->category->name }}</span>
                        </li>
                        <li>
                            <span class="detail-label">Posted</span>
                            <span class="detail-value">{{ $product->created_at->format('j M Y') }}</span>
                        </li>
                    </ul>
                </div>
                
                <div class="safety-tips-box">
                    <div class="safety-tips-header">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Safety Tips</h3>
                    </div>
                    <ul class="safety-list">
                        <li><i class="fas fa-check"></i> Meet in a safe public place</li>
                        <li><i class="fas fa-check"></i> Inspect the item before buying</li>
                        <li><i class="fas fa-check"></i> Pay only after checking the item</li>
                        <li><i class="fas fa-check"></i> Don't share personal financial information</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Similar Products -->
        <div class="similar-products">
            <div class="section-header">
                <h2>Similar Products</h2>
                <a href="{{ route('products.index', ['category' => $product->categoryId]) }}" class="view-all">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="products-row">
                @foreach($similarProducts as $similar)
                <div class="product-card">
                    <a href="{{ route('products.show', $similar->productId) }}" class="product-link">
                        <div class="product-image">
                            @if($similar->images->count() > 0)
                                <img src="{{ asset('storage/' . $similar->images->first()->image_path) }}" alt="{{ $similar->name }}">
                            @else
                                <div class="no-image"></div>
                            @endif
                            <button class="favorite-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                        <div class="product-details">
                            <div class="product-price">{{ number_format($similar->price, 0) }}</div>
                            <h3 class="product-title">{{ $similar->name }}</h3>
                            <div class="product-meta">
                                <span class="product-location">{{ $similar->user->city ?? 'Delhi' }}</span>
                                <span class="product-time">{{ $similar->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image gallery functionality
        const currentImage = document.getElementById('current-image');
        const thumbnails = document.querySelectorAll('.thumbnail');
        
        thumbnails.forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                // Update main image
                currentImage.src = this.getAttribute('data-src');
                
                // Update active thumbnail
                thumbnails.forEach(function(t) {
                    t.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        // Favorite button functionality
        const favoriteBtn = document.getElementById('favorite-btn');
        if (favoriteBtn) {
            favoriteBtn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.classList.add('favorited');
                    this.innerHTML = '<i class="fas fa-heart"></i>Added to Favorites';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.classList.remove('favorited');
                    this.innerHTML = '<i class="far fa-heart"></i>Add to Favorites';
                }
            });
        }
        
        // Product card favorite buttons
        document.querySelectorAll('.product-card .favorite-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    icon.style.color = '#e74c3c';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    icon.style.color = '';
                }
            });
        });
    });
</script>
@endsection