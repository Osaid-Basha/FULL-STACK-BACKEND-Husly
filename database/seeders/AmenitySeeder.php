<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $amenity = [
            'Air Conditioning',
            'Balcony',
            'Barbecue',
            'Bathtub',
            'Cable TV',
            'Dishwasher',
            'Dryer',
            'Fireplace',
            'Fitness Center',
            'Freezer',
            'Garage',
            'Garden',
            'Gym',
            'Heater',
            'Hot Tub',
            'Internet',
            'Jacuzzi',
            'Microwave',
            'Oven',
            'Parking Space',
            'Refrigerator',
            'Sauna',
            'Swimming Pool',
            'Tennis Court',
        ];
        foreach($amenity as $name){
            DB::table('amenities')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
