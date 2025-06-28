<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;

class JSONSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedProducts();
        $this->seedCustomers();
        $this->seedWarehouses();
        $this->seedInventory();
        $this->seedOrders();
        $this->seedOrderItems();
    }

    protected function seedUsers()
    {
        $users = json_decode(File::get(database_path('seeders/data/users.json')), true);

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password']),
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role' => $user['role'],
                    'permissions' => $user['permissions'],
                    'status' => $user['status'],
                ]
            );
        }
    }

    protected function seedProducts()
    {
        $products = json_decode(File::get(database_path('seeders/data/products.json')), true);

        foreach ($products as $product) {
            $category = Category::firstOrCreate(['name' => $product['category']]);
            $subcategory = Subcategory::firstOrCreate([
                'name' => $product['subcategory'],
                'category_id' => $category->id,
            ]);

            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'tax_rate' => $product['tax_rate'],
                    'unit' => $product['unit'],
                    'packaging' => $product['packaging'],
                    'min_order_quantity' => $product['min_order_quantity'],
                    'reorder_level' => $product['reorder_level'],
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                ]
            );
        }
    }

    protected function seedCustomers()
    {
        $customers = json_decode(File::get(database_path('seeders/data/customers.json')), true);

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'type' => $customer['type'],
                    'category' => $customer['category'],
                    'contact_person' => $customer['contact_person'],
                    'phone' => $customer['phone'],
                    'tax_id' => $customer['tax_id'],
                    'payment_terms' => $customer['payment_terms'],
                    'credit_limit' => $customer['credit_limit'],
                    'current_balance' => $customer['current_balance'],
                    'latitude' => $customer['latitude'],
                    'longitude' => $customer['longitude'],
                    'address' => $customer['address'],
                ]
            );
        }
    }

    protected function seedWarehouses()
    {
        $warehouses = json_decode(File::get(database_path('seeders/data/warehouses.json')), true);

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['code' => $warehouse['code']],
                [
                    'name' => $warehouse['name'],
                    'type' => $warehouse['type'],
                    'address' => $warehouse['address'],
                    'manager_email' => $warehouse['manager_email'],
                    'phone' => $warehouse['phone'],
                    'capacity' => $warehouse['capacity'],
                    'latitude' => $warehouse['latitude'],
                    'longitude' => $warehouse['longitude'],
                ]
            );
        }
    }

    protected function seedInventory()
    {
        $inventory = json_decode(File::get(database_path('seeders/data/inventory.json')), true);

        foreach ($inventory as $item) {
            $product = Product::where('sku', $item['sku'])->first();
            $warehouse = Warehouse::where('code', $item['warehouse_code'])->first();

            if ($product && $warehouse) {
                Inventory::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                    ],
                    [
                        'stock_quantity' => $item['stock_quantity'],
                        'reserved_quantity' => $item['reserved_quantity'],
                    ]
                );
            }
        }
    }

    protected function seedOrders()
    {
        $orders = json_decode(File::get(database_path('seeders/data/orders.json')), true);

        foreach ($orders as $order) {
            $customer = Customer::where('email', $order['customer_email'])->first();

            if ($customer) {
                Order::updateOrCreate(
                    ['id' => $order['id']],
                    [
                        'customer_id' => $customer->id,
                        'order_number' => $order['order_number'],
                        'status' => $order['status'],
                        'subtotal' => $order['subtotal'],
                        'discount' => $order['discount'],
                        'tax' => $order['tax'],
                        'total' => $order['total'],
                        'created_at' => $order['created_at'],
                        'updated_at' => $order['updated_at'],
                    ]
                );
            }
        }
    }

    protected function seedOrderItems()
    {
        $items = json_decode(File::get(database_path('seeders/data/order_items.json')), true);

        foreach ($items as $item) {
            $order = Order::find($item['order_id']);
            $product = Product::where('sku', $item['sku'])->first();

            if ($order && $product) {
                OrderItem::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]
                );
            }
        }
    }
}

