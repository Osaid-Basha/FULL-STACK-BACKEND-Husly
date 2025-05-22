<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyImageSeeder extends Seeder
{
    public function run(): void
    {
        $propertyIds = DB::table('properties')->pluck('id');

        foreach ($propertyIds as $propertyId) {
            foreach (range(1, rand(2, 3)) as $i) {
                DB::table('property_images')->insert([
                    'imageUrl' => 'https://via.placeholder.com/600x400?text=Property+' . $propertyId . '+Image+' . $i,
                    'property_id' => $propertyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
