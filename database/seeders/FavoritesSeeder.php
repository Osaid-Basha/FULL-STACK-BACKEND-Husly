<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoritesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (range(1, 10) as $i) {
            DB::table('favorites')->insert([
                'available' => true,
                'property_id' => $i,
                'user_id' => $i,
            ]);
        }

    }
}
