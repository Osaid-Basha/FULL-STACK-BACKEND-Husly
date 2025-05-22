<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $userIds = DB::table('users')->pluck('id');


        foreach (range(1, 10) as $index) {
            DB::table('purchases')->insert([
                'description' => 'Purchase description ' . Str::random(10),
                'user_id' => $userIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
