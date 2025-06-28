<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $inventoryData = json_decode(file_get_contents(database_path('seeders/data/inventory.json')), true);

        foreach ($inventoryData as $item) {
            $product = Product::where('sku', $item['sku'])->first();
            $warehouse = Warehouse::where('code', $item['warehouse_code'])->first();

            if (!$product || !$warehouse) {
                logger()->warning("Missing product or warehouse", [
                    'sku' => $item['sku'],
                    'warehouse_code' => $item['warehouse_code']
                ]);
                continue;
            }

            Inventory::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'quantity' => $item['stock_quantity'],
                'reserved_quantity' => $item['reserved_quantity'] ?? 0,
            ]);
        }
    }
}
