<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['product_id', 'warehouse_id', 'quantity', 'reserved'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getAvailableAttribute()
    {
        return $this->quantity - $this->reserved;
    }
}
