<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $propertyTypes = [
            ['type' => 'Apartment'],
            ['type' => 'House'],
            ['type' => 'Condo'],
            ['type' => 'Townhouse'],
            ['type' => 'Villa'],
            ['type' => 'Duplex'],
            ['type' => 'Studio'],
            ['type' => 'Loft'],
            ['type' => 'Penthouse'],
            ['type' => 'Bungalow'],
        ];
        foreach ($propertyTypes as $propertyType) {
            DB::table('property_types')->insert($propertyType);
        }
    }
}
