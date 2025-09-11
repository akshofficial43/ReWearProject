@extends('admin.layouts.app')

@section('title', 'Order #'.$order->orderId)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h4 mb-0">Order #{{ $order->orderId }}</h1>
    <div class="text-muted">Placed on {{ $order->created_at->format('M j, Y H:i') }}</div>
  </div>
  <div>
    <span class="badge text-bg-secondary">{{ ucfirst($order->status) }}</span>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header fw-semibold">Items</div>
      <div class="card-body">
        @foreach($order->items as $item)
          <div class="d-flex align-items-center gap-3 py-2 border-bottom">
            <div style="width:64px;height:64px;">
              @if($item->product && ($item->product->image || optional($item->product->images->first())->image_path))
                <img src="{{ asset('storage/' . ($item->product->image ?? $item->product->images->first()->image_path)) }}" class="img-thumbnail" style="width:64px;height:64px;object-fit:cover;" alt="{{ $item->product->name }}">
              @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="width:64px;height:64px;">N/A</div>
              @endif
            </div>
            <div class="flex-fill">
              <div class="fw-semibold">{{ $item->product?->name ?? 'Product unavailable' }}</div>
              <div class="text-muted small">₹{{ number_format($item->price,2) }}</div>
            </div>
            <div class="fw-bold">₹{{ number_format(($item->subtotal ?? $item->price * $item->quantity), 2) }}</div>
          </div>
        @endforeach
      </div>
      <div class="card-footer d-flex justify-content-end gap-3">
        <div class="fw-semibold">Total: ₹{{ number_format($order->total, 2) }}</div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-header fw-semibold">Customer</div>
      <div class="card-body">
        <div>{{ $order->user?->name }}</div>
        <div class="text-muted small">ID: {{ $order->userId }}</div>
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-header fw-semibold">Payment</div>
      <div class="card-body">
        @if($order->payment)
          <div class="d-flex justify-content-between"><span>Status</span><span class="fw-semibold">{{ ucfirst($order->payment->payment_status) }}</span></div>
          <div class="d-flex justify-content-between"><span>Method</span><span>{{ ucfirst(str_replace('_',' ', $order->payment->payment_type)) }}</span></div>
          <div class="d-flex justify-content-between"><span>Amount</span><span>₹{{ number_format($order->payment->amount, 2) }}</span></div>
        @else
          <div class="text-muted">No payment info.</div>
        @endif
      </div>
    </div>
    <div class="card">
      <div class="card-header fw-semibold">Status</div>
      <div class="card-body d-flex flex-wrap gap-2">
        @php
          $flow = match($order->status) {
            'pending' => ['processing','cancelled'],
            'processing' => ['shipped','cancelled'],
            'shipped' => ['delivered'],
            default => []
          };
        @endphp
        @forelse($flow as $s)
          <form action="{{ route('admin.orders.update-status', $order->orderId) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ $s }}">
            <button class="btn btn-sm {{ $s==='cancelled' ? 'btn-outline-danger' : 'btn-outline-success' }}" type="submit"
              @if($s==='cancelled') onclick="return confirm('Cancel this order?')" @endif>
              Mark {{ ucfirst($s) }}
            </button>
          </form>
        @empty
          <div class="text-muted">No further actions.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
