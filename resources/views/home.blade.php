@extends('layouts.app')

@section('title', 'ReWear - Buy and Sell Pre-loved Items')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Find anything you need</h1>
            <p>Browse through thousands of listings in your city</p>
            
        </div>
    </section>

    <!-- Browse by Category -->
    <section class="category-section">
        <div class="section-container">
            <div class="section-header">
                <h2>Browse by Category</h2>
                <a href="{{ route('products.index') }}" class="view-all">
                    View All
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            @php
                $categories = \App\Models\Category::whereNull('parent_id')->get();
                $categoryIcons = [
                    "Men's Clothing & Accessories" => 'fa-male',
                    "Women's Clothing & Accessories" => 'fa-female',
                    "Furniture" => 'fa-chair',
                    "Home Décor & Appliances" => 'fa-couch',
                    "Vehicles (Cars, Bikes, Scooters, Accessories)" => 'fa-car',
                    "Mobiles & Electronics (Phones, Laptops, Gadgets)" => 'fa-mobile-alt',
                    "Books, Sports & Hobbies" => 'fa-book',
                    "Kids & Baby Items (Clothing, Toys, Furniture)" => 'fa-baby',
                ];
            @endphp
            <div class="category-slider-wrapper">
                <div class="category-slider">
                    @foreach($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->categoryId]) }}" class="category-card">
                            <div class="category-icon">
                                <i class="fas {{ $categoryIcons[$category->name] ?? 'fa-tag' }}"></i>
                            </div>
                            <div class="category-name-wrapper">
                                <span class="category-name">{{ $category->name }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Fresh Recommendations -->
    <section class="recommendations-section">
        <div class="section-container">
            <div class="section-header">
                <h2>Fresh Recommendations</h2>
                <a href="{{ route('products.index') }}" class="view-all">
                    View All
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="product-grid">
                @forelse($featuredProducts as $product)
                <div class="product-card">
                    <a href="{{ route('products.show', $product->productId) }}" class="product-link">
                        <div class="product-image">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-image"></div>
                            @endif
                            <button class="favorite-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                        <div class="product-details">
                            <div class="product-price">{{ number_format($product->price, 0) }}</div>
                            <h3 class="product-title">{{ $product->name }}</h3>
                            <div class="product-meta">
                                <span class="product-location">{{ $product->location ? $product->location : 'Not specified' }}</span>
                                <span class="product-time">{{ $product->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="no-products">
                    <p>No products available yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="cta-container">
            <h2>Ready to sell something?</h2>
            <p>Post your ad for free and reach millions of buyers</p>
            <a href="{{ route('products.create') }}" class="post-ad-btn">Post Free Ad</a>
        </div>
    </section>
@endsection

