<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('123123123'),
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'role' => 'regular',
            'password' => bcrypt('123123123'),
        ]);
    }
}
