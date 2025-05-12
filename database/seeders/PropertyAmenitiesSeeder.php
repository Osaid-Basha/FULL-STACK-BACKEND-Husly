<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyAmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        for ($i = 1; $i <= 10; $i++) {
            DB::table('property_amenities')->insert([
                'property_id' => rand(1, 10),
                'amenity_id' => rand(1, 24),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


    }
}
