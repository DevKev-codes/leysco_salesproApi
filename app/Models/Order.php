<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected static function boot()
{
    parent::boot();
    
    static::creating(function ($order) {
        $order->order_number = 'ORD-' . now()->format('Y-m') . '-' . str_pad(Order::whereYear('created_at', now()->year)
                                                                                 ->whereMonth('created_at', now()->month)
                                                                                 ->count() + 1, 3, '0', STR_PAD_LEFT);
    });
}
    use HasFactory;
}
