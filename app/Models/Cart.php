<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'cartId';
    
    protected $fillable = [
        'userId',
        // Add any other fillable fields that might be needed
    ];

    public function user()
    {
        // Specify both foreign key and owner key to avoid the user_userId error
        return $this->belongsTo(User::class, 'userId', 'userId');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cartId', 'cartId');
    }
    
    public function addProduct(Product $product, $quantity = 1)
    {
        // Check if product already in cart
        $existingItem = $this->items()->where('productId', $product->productId)->first();
        
        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->save();
            return $existingItem;
        }
        
        // Debug logging
        \Log::info('Adding product to cart', [
            'cartId' => $this->cartId,
            'productId' => $product->productId,
            'quantity' => $quantity
        ]);
        // Add new item to cart
        return CartItem::create([
            'cartId' => $this->cartId,
            'productId' => $product->productId,
            'quantity' => $quantity
        ]);
    }
    
    public function removeProduct(Product $product)
    {
        return $this->items()->where('productId', $product->productId)->delete();
    }
    
    public function getTotalAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
    }
    
    public function checkout()
    {
        if ($this->items->isEmpty()) {
            return null;
        }
        
        // Create a new order
        $order = Order::create([
            'userId' => $this->userId,
            'orderDate' => now(),
            'status' => 'pending'
        ]);
        
        // Transfer cart items to order items
        foreach ($this->items as $item) {
            OrderItem::create([
                'orderId' => $order->orderId,
                'productId' => $item->productId,
                'price' => $item->product->price,
                'quantity' => $item->quantity
            ]);
            
            // Mark product as reserved
            $item->product->update(['status' => 'reserved']);
        }
        
        // Clear the cart
        $this->items()->delete();
        
        return $order;
    }
}