<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Admin",
            "email" => "admin@example.com",
            "password" => bcrypt("admin@123"),
            "role" => User::ADMIN_USER,
           ]);

        User::create([
            "name" => "Test User",
            "email" => "user@example.com",
            "password" => bcrypt("user@123"),
            "role" => User::USER,
           ]);
    }
}
