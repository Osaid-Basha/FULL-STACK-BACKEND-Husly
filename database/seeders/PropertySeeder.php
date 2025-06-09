<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $listingTypeIds = DB::table('listing_types')->pluck('id');
        $propertyTypeIds = DB::table('property_types')->pluck('id');
        $userIds = DB::table('users')->pluck('id');
       
        foreach (range(1, 10) as $i) {
            DB::table('properties')->insert([
                'address' => 'Street ' . $i,
                'city' => 'City ' . $i,
                'title' => 'Property Title ' . $i,
                'landArea' => rand(100, 1000),
                'price' => rand(50000, 300000),
                'bedroom' => rand(1, 5),
                'bathroom' => rand(1, 4),
                'parking' => rand(0, 2),
                'longDescreption' => Str::random(50),
                'shortDescreption' => Str::random(20),
                'constructionArea' => rand(100, 500),
                'livingArea' => rand(100, 400),
                'available' => rand(0, 1),
                'property_listing_id' => $listingTypeIds->random(),
                'property_type_id' => $propertyTypeIds->random(),
                'user_id' => $userIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
