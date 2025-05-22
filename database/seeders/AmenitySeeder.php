<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            'Swimming Pool',
            'Elevator',
            'Garden',
            'Balcony',
            'Parking',
            'Air Conditioning',
            'Security System',
            'Fireplace',
            'Gym',
            'Wi-Fi',
        ];

        foreach ($amenities as $name) {
            DB::table('amenities')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
