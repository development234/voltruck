<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'user',
            'email' => 'user@voltruck.com',
            'password' => bcrypt('user123'),
            'role' => 'user',
        ]);

        \App\Models\User::create([
            'name' => 'admin',
            'email' => 'admin@voltruck.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
    }
}
