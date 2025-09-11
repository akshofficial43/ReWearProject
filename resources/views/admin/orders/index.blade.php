@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div class="d-flex flex-column gap-3 mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <h1 class="h3 mb-0">Orders</h1>
    <form method="GET" class="row g-2 align-items-center">
      <div class="col-auto">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
          <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
        </select>
      </div>
      <div class="col-auto">
        <input type="date" name="date_start" class="form-control form-control-sm" value="{{ request('date_start') }}">
      </div>
      <div class="col-auto">
        <input type="date" name="date_end" class="form-control form-control-sm" value="{{ request('date_end') }}">
      </div>
      <div class="col-auto d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        @if(request()->hasAny(['status','date_start','date_end']))
          <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light">Reset</a>
        @endif
      </div>
    </form>
  </div>

  <div class="row g-3">
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Total Orders</div>
          <div class="fs-4 fw-bold">{{ $totalOrders ?? $orders->total() }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Delivered</div>
          <div class="fs-4 fw-bold">{{ ($statusCounts['delivered'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Processing</div>
          <div class="fs-4 fw-bold">{{ ($statusCounts['processing'] ?? 0) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">Revenue (₹)</div>
          <div class="fs-4 fw-bold">₹{{ number_format(($revenue ?? 0), 2) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Preview</th>
          <th>Date</th>
          <th>Items</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $order)
          <tr>
            <td><a href="{{ route('admin.orders.show', $order->orderId) }}">#{{ $order->orderId }}</a></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                  <i class="fa fa-user text-secondary"></i>
                </div>
                <div>
                  <div class="fw-semibold">{{ $order->user?->name ?? '—' }}</div>
                  <div class="text-muted small">ID: {{ $order->user?->userId ?? '—' }}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center gap-1">
                @php $extra = max($order->items->count() - 3, 0); @endphp
                @foreach($order->items->take(3) as $item)
                  @php
                    $product = $item->product;
                    $thumb = $product?->image ?? ($product?->images->first()->image_path ?? null);
                  @endphp
                  <div class="rounded" style="width:36px;height:36px;overflow:hidden;background:#f1f5f9;">
                    @if($product && $thumb)
                      <img src="{{ asset('storage/' . $thumb) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @endif
                  </div>
                @endforeach
                @if($extra > 0)
                  <span class="badge text-bg-light">+{{ $extra }}</span>
                @endif
              </div>
            </td>
            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $order->items->count() }}</td>
            <td>₹{{ number_format($order->total, 2) }}</td>
            <td>
              @php $p = optional($order->payment); @endphp
              <span class="badge {{ $p->payment_status==='completed' ? 'bg-success' : ($p->payment_status==='failed' ? 'bg-danger' : 'bg-secondary') }}">
                {{ ucfirst($p->payment_status ?? 'pending') }}
              </span>
            </td>
            <td>
              @php
                $statusClass = match($order->status) {
                  'pending' => 'bg-warning',
                  'processing' => 'bg-info',
                  'shipped' => 'bg-primary',
                  'delivered' => 'bg-success',
                  'cancelled' => 'bg-danger',
                  default => 'bg-secondary'
                };
              @endphp
              <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
            </td>
            <td class="d-flex gap-2">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.orders.show', $order->orderId) }}">View</a>
              @php
                $opts = [];
                switch ($order->status) {
                  case 'pending': $opts = ['processing','cancelled']; break;
                  case 'processing': $opts = ['shipped','cancelled']; break;
                  case 'shipped': $opts = ['delivered']; break;
                  default: $opts = []; break;
                }
              @endphp
              @foreach($opts as $opt)
                <form action="{{ route('admin.orders.update-status', $order->orderId) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="status" value="{{ $opt }}">
                  <button type="submit" class="btn btn-sm {{ $opt==='cancelled' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                    @if($opt==='cancelled') onclick="return confirm('Cancel this order?')" @endif>
                    {{ ucfirst($opt) }}
                  </button>
                </form>
              @endforeach
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $orders->links() }}</div>
 </div>
@endsection
