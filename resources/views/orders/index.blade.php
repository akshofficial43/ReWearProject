@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="orders-page">
    <h1 class="page-title">My Orders</h1>

    @if($orders->count() > 0)
        <div class="orders-list">
            @foreach($orders as $order)
                <div class="order-card">
                    <div class="order-head">
                        <div class="left">
                            <div class="order-id">Order #{{ $order->orderId }}</div>
                            <div class="date">{{ $order->created_at->format('M j, Y') }}</div>
                        </div>
                        <div class="right">
                            <span class="chip {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="thumbs">
                            @php $extra = max($order->items->count() - 3, 0); @endphp
                            @foreach($order->items->take(3) as $item)
                                @php
                                    $product = $item->product;
                                    $thumb = $product?->image ?? ($product?->images->first()->image_path ?? null);
                                @endphp
                                <div class="thumb">
                                    @if($product && $thumb)
                                        <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="no-image">N/A</div>
                                    @endif
                                </div>
                            @endforeach
                            @if($extra > 0)
                                <div class="thumb more">+{{ $extra }}</div>
                            @endif
                        </div>

                        <div class="meta">
                            <div class="row"><span class="label">Items</span><span class="value">{{ $order->items->count() }}</span></div>
                            <div class="row"><span class="label">Total</span><span class="value">₹{{ number_format($order->total, 2) }}</span></div>
                            @if($order->payment)
                                <div class="row"><span class="label">Payment</span><span class="value">{{ ucfirst($order->payment->payment_status) }}</span></div>
                            @endif
                        </div>

                        <div class="actions">
                            <a href="{{ route('orders.show', $order->orderId) }}" class="btn primary">View Details</a>
                            <a href="{{ route('products.index') }}" class="btn light">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination">{{ $orders->links() }}</div>
    @else
        <div class="empty-state">
            <p>You haven't placed any orders yet.</p>
            <a href="{{ route('products.index') }}" class="btn primary">Start Shopping</a>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
.orders-page { max-width: 1000px; margin: 24px auto; padding: 0 16px; }
.page-title { margin: 0 0 16px; font-size: 26px; color: #0f172a; }
.orders-list { display: flex; flex-direction: column; gap: 14px; }
.order-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #eef2f7; }
.order-head { display:flex; justify-content:space-between; align-items:center; padding: 14px 16px; background:#fafcff; border-bottom: 1px solid #eef2f7; }
.order-id { font-weight: 700; }
.date { color: #64748b; font-size: 13px; }
.chip { padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
.chip.pending { background:#fff7ed; color:#c2410c; }
.chip.processing { background:#eff6ff; color:#1d4ed8; }
.chip.shipped { background:#f0f9ff; color:#0369a1; }
.chip.delivered { background:#ecfdf5; color:#065f46; }
.chip.cancelled { background:#fef2f2; color:#b91c1c; }
.order-body { display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; padding: 14px 16px; align-items:center; }
.thumbs { display:flex; align-items:center; gap: 8px; }
.thumb { width: 56px; height: 56px; border-radius: 10px; overflow: hidden; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-weight:700; }
.thumb img { width:100%; height:100%; object-fit: cover; }
.thumb.more { background:#e2e8f0; color:#334155; }
.no-image { font-size: 10px; }
.meta { display:flex; flex-direction:column; gap: 6px; }
.meta .row { display:flex; justify-content:space-between; gap: 12px; }
.meta .label { color:#64748b; font-size: 13px; }
.meta .value { font-weight: 700; }
.actions { display:flex; gap: 10px; }
.btn { display:inline-block; padding: 10px 14px; border-radius: 10px; text-decoration:none; font-weight:700; border:1px solid transparent; }
.btn.primary { background:#16a34a; color:#fff; }
.btn.light { background:#f1f5f9; color:#0f172a; border-color:#e2e8f0; }
.empty-state { text-align:center; background:#fff; border:1px dashed #e2e8f0; padding: 32px; border-radius: 12px; }
.pagination { margin: 16px 0; }
@media (max-width: 768px) { .order-body { grid-template-columns: 1fr; align-items: start; } .actions { justify-content: flex-end; } }
</style>
@endsection