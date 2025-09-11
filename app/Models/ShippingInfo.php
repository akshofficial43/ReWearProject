<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingInfo extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'shippingId';
    
    protected $fillable = [
        'orderId',
        'address',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId');
    }
    
    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save();
        
        if ($status == 'shipped') {
            $this->order->status = 'shipped';
            $this->order->save();
        } elseif ($status == 'delivered') {
            $this->order->status = 'delivered';
            $this->order->save();
            
            // Mark products as sold
            foreach ($this->order->items as $item) {
                $item->product->markAsSold();
            }
        }
        
        return true;
    }
    
    public function trackShipment()
    {
        // In a real application, this would integrate with shipping APIs
        return [
            'status' => $this->status,
            'address' => $this->address,
            'lastUpdated' => $this->updated_at
        ];
    }
}