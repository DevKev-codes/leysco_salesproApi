<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = json_decode(file_get_contents(database_path('seeders/data/customers.json')), true);

        foreach ($customers as $customer) {
            Customer::create([
                'name' => $customer['name'],
                'type' => $customer['type'],
                'category' => $customer['category'],
                'contact_person' => $customer['contact_person'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
                'tax_id' => $customer['tax_id'],
                'payment_terms' => $customer['payment_terms'],
                'credit_limit' => $customer['credit_limit'],
                'current_balance' => $customer['current_balance'],
                'latitude' => $customer['latitude'],
                'longitude' => $customer['longitude'],
                'address' => $customer['address'],
            ]);
        }
    }
}