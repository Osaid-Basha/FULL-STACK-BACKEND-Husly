<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReplaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (range(1, 10) as $index) {


            DB::table('replays')->insert([
                'message_content' => Str::random(10),
                'user_id' => $index, // Assuming you have users with IDs from 1 to 10
                'review_id' => $index, // Assuming you have reviews with IDs from 1 to 10
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
