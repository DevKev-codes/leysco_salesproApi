<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            WarehouseSeeder::class,
            InventorySeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
           
        ]);
    }
}