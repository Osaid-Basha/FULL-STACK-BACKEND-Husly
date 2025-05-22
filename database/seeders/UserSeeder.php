<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Mohammad',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'status' => 1,
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Ahmad',
                'last_name' => 'Agent',
                'email' => 'agent@example.com',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'status' => 1,
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Sara',
                'last_name' => 'Buyer',
                'email' => 'buyer@example.com',
                'password' => Hash::make('password123'),
                'role_id' => 3,
                'status' => 0,
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
