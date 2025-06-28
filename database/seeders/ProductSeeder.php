<?php

namespace Database\Seeders;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = json_decode(file_get_contents(database_path('seeders/data/products.json')), true);

        foreach ($products as $product) {
            $category = Category::firstWhere('name', $product['category']);
            $subcategory = Subcategory::firstWhere('name', $product['subcategory']);

            if (!$category || !$subcategory) {
    logger()->warning("Missing category or subcategory for product: {$product['name']}");
    continue;
}

            Product::create([
                'sku' => $product['sku'],
                'name' => $product['name'],
                'category_id' => $category?->id,
                'subcategory_id' => $subcategory?->id,
                'description' => $product['description'],
                'price' => $product['price'],
                'tax_rate' => $product['tax_rate'],
                'unit' => $product['unit'],
                'packaging' => $product['packaging'],
                'min_order_quantity' => $product['min_order_quantity'],
                'reorder_level' => $product['reorder_level'],
            ]);
        }
    }
}
