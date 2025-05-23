<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        foreach (range(1, 10) as $i) {
            DB::table('role_users')->insert([
                'user_id' => $i,
                'role_id' => 1, // Assuming 1 is the ID for the buyer role
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
