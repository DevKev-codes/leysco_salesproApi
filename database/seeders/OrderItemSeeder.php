<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = json_decode(file_get_contents(database_path('seeders/data/order_items.json')), true);

        foreach ($items as $item) {
            if (!isset($item['order_id'], $item['sku'], $item['quantity'], $item['price'])) {
                logger()->warning("Incomplete order item data", ['item' => $item]);
                continue;
            }

            $order = Order::find($item['order_id']);
            $product = Product::where('sku', $item['sku'])->first();

            if (!$order || !$product) {
                logger()->warning("Missing order or product for order item", [
                    'order_id' => $item['order_id'],
                    'sku' => $item['sku'],
                ]);
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }
    }
}
