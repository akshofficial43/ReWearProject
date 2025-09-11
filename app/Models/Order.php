<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'orderId';
    
    protected $fillable = [
        'userId',
        'orderDate',
        'status'
    ];

    protected $casts = [
        'orderDate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'orderId');
    }
    
    public function payment()
    {
        return $this->hasOne(Payment::class, 'orderId');
    }
    
    public function shipping()
    {
        return $this->hasOne(ShippingInfo::class, 'orderId');
    }
    
    public function cancelOrder()
    {
        if ($this->status != 'shipped' && $this->status != 'delivered') {
            $this->status = 'cancelled';
            $this->save();
            
            // Return products to available status
            foreach ($this->items as $item) {
                $item->product->update(['status' => 'available']);
            }
            
            return true;
        }
        return false;
    }
    
    public function getTotalAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }
}