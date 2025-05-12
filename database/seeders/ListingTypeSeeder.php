<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $listingTypes = [
            ['type' => 'sale'],
            ['type' => 'rent'],

        ];
        foreach ($listingTypes as $listingType) {
            DB::table('listing_types')->insert($listingType);
            
        }
    }
}
