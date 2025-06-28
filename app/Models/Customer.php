<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Order;



class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'category', 'contact_person', 'phone', 'email',
        'tax_id', 'payment_terms', 'credit_limit', 'current_balance',
        'latitude', 'longitude', 'address'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}


