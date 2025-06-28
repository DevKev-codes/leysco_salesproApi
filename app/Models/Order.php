<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'user_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'status',
    ];

    // An order belongs to a customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // If you have an OrderItem model:
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

