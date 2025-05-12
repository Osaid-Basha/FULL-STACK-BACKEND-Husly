<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (range(1, 10) as $index) {
            DB::table('reviews')->insert([
                'content' => 'This is a review content for review ' . $index,
                'title' => 'Review Title ' . $index,
                'rating' => rand(1, 5),
                'buying_id' => $index, // Assuming you have buying requests with IDs from 1 to 10
                'created_at' => now(),
                'updated_at' => now(),
                // Assuming you have buying requests with IDs from 1 to 10
            ]);
        }
    }
}
