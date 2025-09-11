<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Auth::user()
            ->orders()
            ->with(['items.product.images', 'payment'])
            ->latest()
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show($orderId)
    {
        $order = Order::with(['items.product', 'payment', 'shipping'])->findOrFail($orderId);
        
        // Make sure the user can only see their own orders
        if (Auth::id() !== $order->userId && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('orders.show', compact('order'));
    }

    public function complete($orderId)
    {
        $order = Order::with(['items.product', 'payment'])->findOrFail($orderId);
        if (Auth::id() !== $order->userId && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('orders.complete', compact('order'));
    }

    public function cancel($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Make sure the user can only cancel their own orders
        if (Auth::id() !== $order->userId && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($order->cancelOrder()) {
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        }
        
        return redirect()->back()->with('error', 'Cannot cancel this order as it has been shipped or delivered.');
    }
}