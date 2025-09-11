
@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="order-view">
    <div class="card">
        <div class="card-header">
            <div class="head-left">
                <h1>Order #{{ $order->orderId }}</h1>
                <div class="muted">Placed on {{ $order->created_at->format('M j, Y') }}</div>
            </div>
            <div class="head-right">
                <span class="status {{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>

        <div class="summary">
            <div class="summary-grid">
                        <div>
                            <div class="label">Items</div>
                            <div class="value">{{ $order->items->count() }}</div>
                        </div>
                <div>
                    <div class="label">Total</div>
                    <div class="value">₹{{ number_format($order->total, 2) }}</div>
                </div>
                <div>
                    <div class="label">Payment</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', optional($order->payment)->payment_status ?? 'pending')) }}</div>
                </div>
                <div>
                    <div class="label">Method</div>
                    <div class="value">{{ ucfirst(str_replace('_', ' ', optional($order->payment)->payment_type ?? '—')) }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Items</h2>
            <div class="items">
                @foreach($order->items as $item)
                    @php
                        $product = $item->product;
                        $thumb = $product?->image ?? ($product?->images->first()->image_path ?? null);
                    @endphp
                    <div class="item">
                        <div class="thumb">
                            @if($product && $thumb)
                                <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-image">No Image</div>
                            @endif
                        </div>
                        <div class="info">
                            <div class="name">
                                @if($product)
                                    <a href="{{ route('products.show', $product->productId) }}">{{ $product->name }}</a>
                                @else
                                    Product no longer available
                                @endif
                            </div>
                              <div class="meta">₹{{ number_format($item->price, 2) }}</div>
                        </div>
                        <div class="line-total">₹{{ number_format(($item->subtotal ?? $item->price * $item->quantity), 2) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="totals">
                <div class="row">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</span>
                </div>
                <div class="row grand">
                    <span>Total</span>
                    <span>₹{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="section two-col">
            <div class="panel">
                <h3>Shipping Information</h3>
                @if($order->shipping)
                    <div class="line"><span class="label">Address</span><span>{{ $order->shipping->address }}</span></div>
                    <div class="line"><span class="label">Status</span><span>{{ ucfirst($order->shipping->status) }}</span></div>
                @else
                    <div class="muted">No shipping information available.</div>
                @endif
            </div>
            <div class="panel">
                <h3>Payment Information</h3>
                @if($order->payment)
                    <div class="line"><span class="label">Method</span><span>{{ ucfirst(str_replace('_', ' ', $order->payment->payment_type)) }}</span></div>
                    <div class="line"><span class="label">Status</span><span>{{ ucfirst($order->payment->payment_status) }}</span></div>
                    <div class="line"><span class="label">Amount</span><span>₹{{ number_format($order->payment->amount, 2) }}</span></div>
                @else
                    <div class="muted">No payment information available.</div>
                @endif
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('orders.index') }}" class="btn secondary">Back to Orders</a>
            @if($order->status !== 'cancelled' && $order->status !== 'delivered' && $order->status !== 'shipped')
                <form action="{{ route('orders.cancel', $order->orderId) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    <button type="submit" class="btn danger">Cancel Order</button>
                </form>
            @endif
            <button class="btn light" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.order-view { max-width: 1000px; margin: 24px auto; padding: 0 16px; }
.card { background: #fff; border-radius: 16px; box-shadow: 0 6px 24px rgba(0,0,0,0.06); overflow: hidden; }
.card-header { display:flex; align-items:center; justify-content:space-between; padding: 20px 24px; border-bottom: 1px solid #eef2f7; }
.card-header h1 { margin: 0; font-size: 22px; }
.muted { color:#64748b; font-size: 14px; }
.status { padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
.status.pending { background:#fff7ed; color:#c2410c; }
.status.processing { background:#eff6ff; color:#1d4ed8; }
.status.shipped { background:#f0f9ff; color:#0369a1; }
.status.delivered { background:#ecfdf5; color:#065f46; }
.status.cancelled { background:#fef2f2; color:#b91c1c; }
.summary { padding: 12px 24px; background: #f8fafc; border-bottom: 1px solid #eef2f7; }
.summary-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.summary .label { color:#64748b; font-size:12px; }
.summary .value { font-weight:700; }
.section { padding: 20px 24px; border-bottom: 1px solid #eef2f7; }
.section h2 { margin: 0 0 12px; font-size: 18px; }
.items { display:flex; flex-direction:column; gap: 12px; }
.item { display:grid; grid-template-columns: 72px 1fr auto; gap: 12px; align-items:center; padding: 10px; border:1px solid #f1f5f9; border-radius: 12px; }
.thumb img { width:72px; height:72px; object-fit:cover; border-radius:8px; }
.no-image { width:72px; height:72px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:#94a3b8; border-radius:8px; font-size:12px; }
.info .name a { color:#0f172a; font-weight:600; text-decoration:none; }
.info .meta { color:#475569; font-size: 14px; margin-top: 4px; }
.line-total { font-weight:700; }
.totals { margin-top: 12px; max-width: 420px; margin-left:auto; display:flex; flex-direction:column; gap:8px; }
.totals .row { display:flex; align-items:center; justify-content:space-between; }
.totals .grand { font-size: 18px; font-weight: 800; }
.two-col { display:grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.panel { background:#fcfdff; border:1px solid #eef2f7; border-radius:12px; padding: 16px; }
.panel h3 { margin-top:0; font-size: 16px; }
.panel .line { display:flex; gap: 10px; margin: 6px 0; }
.panel .line .label { width: 90px; color:#64748b; }
.actions { display:flex; gap: 10px; align-items:center; padding: 16px 24px; }
.btn { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:10px; border:none; cursor:pointer; font-weight:600; text-decoration:none; }
.btn.secondary { background:#e2e8f0; color:#0f172a; }
.btn.danger { background:#ef4444; color:#fff; }
.btn.light { background:#f1f5f9; color:#0f172a; }
@media (max-width: 768px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } .two-col { grid-template-columns: 1fr; } }
</style>
@endsection