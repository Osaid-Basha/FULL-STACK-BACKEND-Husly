<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegotiationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach(range(1, 10) as $i) {

            DB::table('negotiations')->insert([
                'status' => rand(0, 1),
                'proposed_price' => rand(100000, 500000),
                'property_id' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }

    }
}
