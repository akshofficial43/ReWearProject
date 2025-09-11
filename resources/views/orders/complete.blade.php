@extends('layouts.app')

@section('title', 'Thank You!')

@section('content')
<div class="order-complete">
  <div class="complete-card">
    <div class="icon-wrap">
      <i class="fas fa-check-circle"></i>
    </div>
    <h1>Thank you for your purchase!</h1>
    <p class="subtitle">Your order #{{ $order->orderId }} has been placed successfully.</p>

    <div class="summary">
      <div class="row">
        <div class="col">
          <span class="label">Date</span>
          <span class="value">{{ $order->created_at->format('M j, Y') }}</span>
        </div>
        <div class="col">
          <span class="label">Items</span>
          <span class="value">{{ $order->items->count() }}</span>
        </div>
        <div class="col">
          <span class="label">Total</span>
          <span class="value">₹{{ number_format($order->total, 2) }}</span>
        </div>
        <div class="col">
          <span class="label">Payment</span>
          <span class="value">{{ ucfirst(str_replace('_', ' ', optional($order->payment)->payment_status ?? 'pending')) }}</span>
        </div>
      </div>
    </div>

    <div class="actions">
      <a href="{{ route('orders.show', $order->orderId) }}" class="btn-primary">View Order</a>
      <a href="{{ route('products.index') }}" class="btn-secondary">Continue Shopping</a>
    </div>

    <div class="items">
      @foreach($order->items as $item)
        <div class="item">
          <div class="thumb">
            @if($item->product && $item->product->image)
              <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
            @else
              <div class="no-image">No Image</div>
            @endif
          </div>
          <div class="info">
            <div class="name">{{ $item->product?->name ?? 'Product unavailable' }}</div>
            <div class="meta">₹{{ number_format($item->price, 2) }}</div>
          </div>
          <div class="line-total">₹{{ number_format($item->quantity * $item->price, 2) }}</div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
.order-complete { max-width: 900px; margin: 32px auto; padding: 0 16px; }
.complete-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
.icon-wrap { text-align: center; color: #16a34a; font-size: 64px; margin-bottom: 8px; }
.complete-card h1 { text-align: center; font-size: 28px; margin: 0 0 6px; }
.subtitle { text-align: center; color: #64748b; margin-bottom: 20px; }
.summary { background: #f8fafc; border-radius: 12px; padding: 12px; margin-bottom: 20px; }
.summary .row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.summary .label { display: block; color: #64748b; font-size: 12px; }
.summary .value { font-weight: 600; }
.actions { display: flex; gap: 12px; justify-content: center; margin: 16px 0 8px; }
.btn-primary, .btn-secondary { display: inline-block; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; }
.btn-primary { background: #16a34a; color: #fff; }
.btn-secondary { background: #e2e8f0; color: #111827; }
.items { margin-top: 16px; }
.item { display: grid; grid-template-columns: 72px 1fr auto; gap: 12px; align-items: center; padding: 12px; border-bottom: 1px solid #f1f5f9; }
.thumb img { width: 72px; height: 72px; object-fit: cover; border-radius: 8px; }
.no-image { width: 72px; height: 72px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:#94a3b8; border-radius:8px; font-size:12px; }
.line-total { font-weight: 700; }
@media (max-width: 640px) { .summary .row { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection
