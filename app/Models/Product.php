<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku', 'name', 'description', 'price', 'cost_price', 
        'reorder_level', 'category_id', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }
    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}


    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query)
    {
        return $query->whereHas('inventory', function($q) {
            $q->whereRaw('quantity <= reorder_level');
        });
    }

    public function scopeSearch(Builder $query, string $search)
    {
        return $query->whereRaw('MATCH(name, description) AGAINST(? IN BOOLEAN MODE)', [$search]);
    }
}

