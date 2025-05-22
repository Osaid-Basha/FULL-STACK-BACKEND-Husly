<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'For Sale',
            'For Rent',
            'Wanted to Buy',
        ];

        foreach ($types as $type) {
            DB::table('listing_types')->insert([
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
