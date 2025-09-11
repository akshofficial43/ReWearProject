@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        @if($cart->items->count() > 0)
            <div class="cart-items">
                @foreach($cart->items as $item)
                    <div class="cart-item">
                        <div class="cart-item-image">
                            @if($item->product && $item->product->images->count() > 0)
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}">
                            @else
                                <div class="no-image">No Image</div>
                            @endif
                        </div>
                        <div class="cart-item-details">
                            @if($item->product)
                                <h3><a href="{{ route('products.show', $item->product->productId) }}">{{ $item->product->name }}</a></h3>
                                <p>Condition: {{ ucfirst(str_replace('_', ' ', $item->product->condition)) }}</p>
                                <p>Price: ₹{{ number_format($item->product->price, 0) }}</p>
                            @else
                                <h3>Product not available</h3>
                                <p>This product may have been removed</p>
                            @endif
                        </div>
                        
                        <div class="cart-item-subtotal">
                            @if($item->product)
                                <p><strong>Subtotal:</strong> ₹{{ number_format($item->product->price * $item->quantity, 0) }}</p>
                            @else
                                <p><strong>Subtotal:</strong> ₹0</p>
                            @endif
                        </div>
                        <div class="cart-item-actions">
                            <!-- Make this a standalone form not nested inside the update form -->
                            <form action="{{ route('cart.remove', $item->product ? $item->product->productId : 0) }}" method="POST" class="remove-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" onclick="return confirm('Remove this item from cart?')">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
                
            <div class="cart-summary">
                <div class="cart-total">
                    <h3>Total: ₹{{ number_format($cart->items->sum(function($item) {
                        return $item->product ? $item->quantity * $item->product->price : 0;
                    }), 0) }}</h3>
                </div>
                <div class="cart-actions">
                    <a href="{{ route('cart.checkout') }}" class="btn-primary">Proceed to Checkout</a>
                </div>
            </div>
        @else
            <div class="empty-state">
                <p>Your cart is empty.</p>
                <a href="{{ route('products.index') }}" class="btn-primary">Start Shopping</a>
            </div>
        @endif
    </div>

    <style>
    .cart-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }

    .cart-container h1 {
        margin-bottom: 30px;
        font-size: 28px;
        color: #002f34;
    }

    .alert {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .cart-items {
        margin-bottom: 30px;
    }

    .cart-item {
        display: grid;
        grid-template-columns: 100px 3fr 1fr 100px;
        align-items: center;
        padding: 20px 0;
        border-bottom: 1px solid #eee;
        gap: 15px;
    }

    .cart-item-image {
        width: 100px;
        height: 100px;
    }

    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .no-image {
        width: 100%;
        height: 100%;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 12px;
        border-radius: 4px;
    }

    .cart-item-details h3 {
        margin: 0 0 8px;
        font-size: 16px;
    }

    .cart-item-details h3 a {
        color: #002f34;
        text-decoration: none;
    }

    .cart-item-details h3 a:hover {
        color: #23e5db;
    }

    .cart-item-details p {
        margin: 4px 0;
        color: #727c7e;
        font-size: 14px;
    }

    .cart-item-subtotal p {
        font-size: 15px;
    }

    .cart-item-actions {
        text-align: right;
    }
    
    .remove-form {
        display: inline-block;
    }

    .cart-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .cart-total h3 {
        font-size: 22px;
        color: #002f34;
    }

    .cart-actions {
        display: flex;
        gap: 15px;
    }

    .btn-primary, .btn-secondary, .btn-danger {
        display: inline-block;
        padding: 12px 20px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        border: none;
    }

    .btn-primary {
        background-color: #23e5db;
        color: #002f34;
    }

    .btn-secondary {
        background-color: #f2f4f5;
        color: #002f34;
        border: 1px solid #ddd;
    }

    .btn-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .btn-primary:hover, .btn-secondary:hover, .btn-danger:hover {
        opacity: 0.9;
    }

    .empty-state {
        text-align: center;
        padding: 40px 0;
    }

    .empty-state p {
        font-size: 18px;
        color: #727c7e;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .cart-item {
            grid-template-columns: 80px 1fr;
            gap: 15px;
            padding: 15px 0;
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            grid-row: span 2;
        }
        
        .cart-item-details {
            grid-column: 2;
        }
        
        .cart-item-subtotal {
            grid-column: 2;
            margin-top: 10px;
        }
        
        .cart-item-actions {
            grid-column: 1 / -1;
            text-align: right;
            margin-top: 10px;
        }
        
        .cart-summary {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .cart-actions {
            width: 100%;
        }
        
        .btn-primary, .btn-secondary {
            flex: 1;
            text-align: center;
        }
    }
    </style>
@endsection