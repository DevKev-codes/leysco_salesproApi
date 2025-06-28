<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $warehouses = json_decode(file_get_contents(database_path('seeders/data/warehouses.json')), true);

        foreach ($warehouses as $warehouse) {
            Warehouse::create([
                'code' => $warehouse['code'],
                'name' => $warehouse['name'],
                'type' => $warehouse['type'],
                'address' => $warehouse['address'],
                'manager_email' => $warehouse['manager_email'],
                'phone' => $warehouse['phone'],
                'capacity' => $warehouse['capacity'],
                'latitude' => $warehouse['latitude'],
                'longitude' => $warehouse['longitude'],
            ]);
        }
    }
}