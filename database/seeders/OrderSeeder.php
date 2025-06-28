<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = json_decode(file_get_contents(database_path('seeders/data/orders.json')), true);

        foreach ($orders as $order) {
            $customer = Customer::where('email', $order['customer_email'])->first();
            $user = User::first(); // You can refine this if you want a specific user

            if (!$customer || !$user) {
                logger()->warning("Missing customer or user for order", [
                    'order_number' => $order['order_number'],
                    'customer_email' => $order['customer_email'],
                ]);
                continue;
            }

            Order::create([
                'order_number'      => $order['order_number'],
                'customer_id'       => $customer->id,
                'user_id'           => $user->id,
                'subtotal'          => $order['subtotal'],
                'tax_amount'        => $order['tax'],
                'discount_amount'   => $order['discount'],
                'total_amount'      => $order['total'],
                'status'            => $order['status'],
                'created_at'        => $order['created_at'],
                'updated_at'        => $order['updated_at'],
            ]);
        }
    }
}
