<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get or create the cart for current user
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['userId' => $user->userId]);
        
        // Ensure we load the cart with its items and related products
        $cart->load('items.product');
        
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($productId);
        
        if ($product->status !== 'available') {
            return redirect()->back()->with('error', 'This product is not available.');
        }

        // Get or create cart
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['userId' => $user->userId]);
        
        // Add product to cart
        $cart->addProduct($product, $request->quantity);

        return redirect()->back()->with('success', 'Product added to cart.');
    }

    public function remove($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Get cart if it exists
        $user = Auth::user();
        $cart = Cart::where('userId', $user->userId)->first();
        
        if ($cart) {
            $cart->removeProduct($product);
            return redirect()->back()->with('success', 'Product removed from cart.');
        }

        return redirect()->back()->with('error', 'No cart found to remove product from.');
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
        ]);
        
        $user = Auth::user();
        $cart = Cart::where('userId', $user->userId)->first();
        
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Cart not found.');
        }
        
        foreach ($request->quantity as $itemId => $quantity) {
            $cartItem = $cart->items()->find($itemId);
            
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        }
        
        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }
    
    public function checkout()
    {
        $user = Auth::user();
        $cart = Cart::where('userId', $user->userId)->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        // Load products for proper total calculation
        $cart->load('items.product');
        
        return view('cart.checkout', compact('cart'));
    }
    
    public function processCheckout(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'payment_type' => 'required|string|in:credit_card,paypal,bank_transfer',
        ]);
        
        $user = Auth::user();
        $cart = Cart::where('userId', $user->userId)->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        // Load products for proper checkout
        $cart->load('items.product');
        
        $order = $cart->checkout();
        
        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Failed to create order.');
        }
        
        // Create shipping information
        $shipping = $order->shipping()->create([
            'address' => $request->address,
            'status' => 'pending'
        ]);
        
        // Calculate total from order items to ensure accuracy
        $totalAmount = $order->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        
        // Create payment
        $payment = $order->payment()->create([
            'payment_type' => $request->payment_type,
            'payment_status' => 'pending',
            'amount' => $totalAmount
        ]);
        
        // Process payment (in a real app, this would integrate with payment gateways)
        if (method_exists($payment, 'makePayment')) {
            $payment->makePayment($request->payment_type);
        }
        
        return redirect()->route('orders.complete', $order->orderId)
            ->with('success', 'Order placed successfully!');
    }
}