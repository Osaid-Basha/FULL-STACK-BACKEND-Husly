<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['type' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'agent', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'buyer', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
