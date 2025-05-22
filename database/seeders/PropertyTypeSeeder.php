<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Apartment',
            'Villa',
            'Land',
            'Commercial',
            'Office',
            'Studio',
        ];

        foreach ($types as $type) {
            DB::table('property_types')->insert([
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
