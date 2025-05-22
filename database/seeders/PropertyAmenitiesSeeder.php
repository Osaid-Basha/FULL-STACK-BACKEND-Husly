<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyAmenitiesSeeder extends Seeder
{
    public function run(): void
    {
        $propertyIds = DB::table('properties')->pluck('id');
        $amenityIds = DB::table('amenities')->pluck('id');

        foreach ($propertyIds as $property_id) {
            // نربط كل عقار بـ 2 إلى 4 مرافق عشوائية
            $randomAmenities = $amenityIds->random(rand(2, 4));

            foreach ($randomAmenities as $amenity_id) {
                DB::table('property_amenities')->insert([
                    'property_id' => $property_id,
                    'amenity_id' => $amenity_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
