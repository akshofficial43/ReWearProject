<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'paymentId';
    
    protected $fillable = [
        'orderId',
        'payment_type',
        'payment_status',
        'amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId');
    }
    
    public function makePayment(string $paymentType)
    {
        $this->payment_type = $paymentType;
        $this->payment_status = 'completed';
        $this->save();
        
        // Update order status
        $this->order->status = 'processing';
        $this->order->save();
        
        return true;
    }
    
    public function refundPayment()
    {
        if ($this->payment_status == 'completed') {
            $this->payment_status = 'refunded';
            $this->save();
            
            // Update order
            $this->order->status = 'cancelled';
            $this->order->save();
            
            return true;
        }
        return false;
    }
}