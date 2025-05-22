<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoritesSeeder extends Seeder
{
    public function run(): void
    {
        $propertyIds = DB::table('properties')->pluck('id');
        $userIds = DB::table('users')->pluck('id');

        $usedPairs = [];

        foreach (range(1, 10) as $i) {
            $property_id = $propertyIds->random();
            $user_id = $userIds->random();

            $key = $user_id . '-' . $property_id;
            if (in_array($key, $usedPairs)) continue;
            $usedPairs[] = $key;

            DB::table('favorites')->insert([
                'property_id' => $property_id,
                'user_id' => $user_id,
                'available' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
