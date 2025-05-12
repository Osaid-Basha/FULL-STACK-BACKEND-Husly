<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuyingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    foreach (range(1, 10) as $index) {

        DB::table('buying_requests')->insert([

            'status' => rand(0, 1), // Randomly set status to 0 or 1
            'type' => 'Type ' . $index, // Example type
            'negotiation_id' => $index, // Assuming you have negotiations with IDs from 1 to 10
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    }
}
