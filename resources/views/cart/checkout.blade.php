@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="checkout-container">
    <h1>Checkout</h1>
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="checkout-grid">
        <!-- Left Column - Form -->
        <div class="checkout-form-column">
            <!-- Using direct URL instead of route name to avoid errors -->
            <form action="/cart/checkout/process" method="POST" id="checkout-form">
                @csrf
                
                <div class="form-section">
                    <h2>Shipping Information</h2>
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ Auth::user()->phone ?? '' }}" required>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Complete Address</label>
                        <textarea id="address" name="address" rows="3" required>{{ Auth::user()->address ?? '' }}</textarea>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="{{ Auth::user()->city ?? '' }}" required>
                            @error('city')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group half">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ Auth::user()->postal_code ?? '' }}" required>
                            @error('postal_code')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Payment Method</h2>
                    
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" id="payment_type_credit_card" name="payment_type" value="credit_card" checked>
                            <label for="payment_type_credit_card">
                                <i class="fas fa-credit-card"></i>
                                Credit/Debit Card
                            </label>
                        </div>
                        
                        <div class="payment-option">
                            <input type="radio" id="payment_type_paypal" name="payment_type" value="paypal">
                            <label for="payment_type_paypal">
                                <i class="fab fa-paypal"></i>
                                PayPal
                            </label>
                        </div>
                        
                        <div class="payment-option">
                            <input type="radio" id="payment_type_bank_transfer" name="payment_type" value="bank_transfer">
                            <label for="payment_type_bank_transfer">
                                <i class="fas fa-university"></i>
                                Bank Transfer
                            </label>
                        </div>
                    </div>
                    @error('payment_type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ url('/cart') }}" class="btn-secondary">Back to Cart</a>
                    <button type="submit" class="btn-primary">Complete Order</button>
                </div>
            </form>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div class="order-summary-column">
            <div class="order-summary">
                <h2>Order Summary</h2>
                
                <div class="summary-items">
                    @foreach($cart->items as $item)
                        @if($item->product)
                            <div class="summary-item">
                                <div class="summary-item-image">
                                    @if($item->product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" alt="{{ $item->product->name }}">
                                    @else
                                        <div class="no-image"></div>
                                    @endif
                                </div>
                                <div class="summary-item-details">
                                    <h3>{{ $item->product->name }}</h3>
                                </div>
                                <div class="summary-item-price">
                                    ₹{{ number_format($item->product->price * $item->quantity, 0) }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($cart->items->sum(function($item) {
                            return $item->product ? $item->product->price * $item->quantity : 0;
                        }), 0) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>₹100</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>₹{{ number_format($cart->items->sum(function($item) {
                            return $item->product ? $item->product->price * $item->quantity : 0;
                        }) + 100, 0) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="secure-checkout">
                <div class="secure-icons">
                    <i class="fas fa-lock"></i>
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                </div>
                <p>Your payment information is processed securely. We do not store credit card details nor have access to your credit card information.</p>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.checkout-container h1 {
    margin-bottom: 30px;
    font-size: 28px;
    color: #002f34;
}

.alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

/* Form Column Styles */
.checkout-form-column {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.form-section h2 {
    margin-bottom: 20px;
    font-size: 20px;
    color: #002f34;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-group.half {
    flex: 1;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #002f34;
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="number"],
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

.error-message {
    display: block;
    color: #dc3545;
    font-size: 14px;
    margin-top: 5px;
}

/* Payment Options */
.payment-options {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}

.payment-option {
    display: flex;
    align-items: center;
}

.payment-option input[type="radio"] {
    margin-right: 10px;
    width: 20px;
    height: 20px;
}

.payment-option label {
    display: flex;
    align-items: center;
    margin: 0;
    cursor: pointer;
}

.payment-option i {
    margin-right: 10px;
    font-size: 18px;
    color: #002f34;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
}

/* Order Summary Column Styles */
.order-summary-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.order-summary {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    padding: 25px;
}

.order-summary h2 {
    margin-bottom: 20px;
    font-size: 20px;
    color: #002f34;
}

.summary-items {
    margin-bottom: 20px;
    max-height: 300px;
    overflow-y: auto;
}

.summary-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.summary-item-image {
    width: 60px;
    height: 60px;
    margin-right: 15px;
}

.summary-item-image img {
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

.summary-item-details {
    flex: 1;
}

.summary-item-details h3 {
    font-size: 16px;
    margin: 0 0 5px;
    color: #002f34;
}

.summary-item-details p {
    margin: 0;
    color: #727c7e;
    font-size: 14px;
}

.summary-item-price {
    font-weight: 600;
    color: #002f34;
    font-size: 16px;
}

.summary-totals {
    padding-top: 15px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    color: #727c7e;
}

.summary-row.total {
    font-weight: 600;
    font-size: 18px;
    color: #002f34;
    border-top: 1px solid #eee;
    padding-top: 15px;
    margin-top: 15px;
}

.secure-checkout {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.secure-icons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 15px;
}

.secure-icons i {
    font-size: 24px;
    color: #002f34;
}

.secure-checkout p {
    text-align: center;
    font-size: 14px;
    color: #727c7e;
    margin: 0;
}

/* Button Styles */
.btn-primary, .btn-secondary {
    display: inline-block;
    padding: 14px 25px;
    border-radius: 4px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    font-size: 16px;
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

.btn-primary:hover, .btn-secondary:hover {
    opacity: 0.9;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 1fr 350px;
    }
}

@media (max-width: 768px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .checkout-form-column, .order-summary {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 15px;
    }
    
    .btn-secondary, .btn-primary {
        width: 100%;
        text-align: center;
    }
}
</style>
@endsection