<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegotiationSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id');
        $propertyIds = DB::table('properties')->pluck('id');

        $statuses = ['pending', 'accepted', 'rejected'];

        foreach (range(1, 10) as $i) {
            DB::table('negotiations')->insert([
                'user_id' => $userIds->random(),
                'status' => $statuses[array_rand($statuses)],
                'proposed_price' => rand(30000, 200000),
                'property_id' => $propertyIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
