<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = json_decode(file_get_contents(database_path('seeders/data/users.json')), true);

        foreach ($users as $user) {
            User::create([
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role'],
                'permissions' => $user['permissions'],
                'status' => $user['status'],
            ]);
        }
    }
}