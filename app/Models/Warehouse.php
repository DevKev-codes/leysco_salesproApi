<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    /* ─────── Relationships ─────── */

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'inventory')
                    ->withPivot('stock_quantity', 'reserved_quantity')
                    ->withTimestamps();
    }
}

