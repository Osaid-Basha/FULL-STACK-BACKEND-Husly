<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $buyingIds = DB::table('buying_requests')->pluck('id');
        $userIds = DB::table('users')->pluck('id');

        foreach ($buyingIds as $buying_id) {
            DB::table('reviews')->insert([
                'content' => Str::random(50),
                'title' => 'Review Title ' . rand(1, 100),
                'rating' => rand(1, 5),
                'buying_id' => $buying_id,
                'user_id' => $userIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
