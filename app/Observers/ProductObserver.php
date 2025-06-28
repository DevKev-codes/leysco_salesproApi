<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;

class ProductObserver
{
    public function updating(Product $product)
    {
        if ($product->isDirty('stock_quantity') || $product->isDirty('reorder_level')) {
            $product->inventory()->with('warehouse')->get()->each(function ($inventory) use ($product) {
                $available = $inventory->stock_quantity - $inventory->reserved_quantity;

                if ($available <= $product->reorder_level) {
                    $this->notifyLowStock($product, $inventory);
                }
            });
        }
    }

    protected function notifyLowStock(Product $product, Inventory $inventory)
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['inventory_manager', 'admin']);
        })->get();

        Notification::send($users, new LowStockNotification($product,$inventory));
    }
}
