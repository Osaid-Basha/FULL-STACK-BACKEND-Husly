<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReplaySeeder extends Seeder
{
    public function run(): void
    {
        $reviewIds = DB::table('reviews')->pluck('id');
        $userIds = DB::table('users')->pluck('id');

        foreach ($reviewIds as $review_id) {
            DB::table('replays')->insert([
                'message_content' => 'Thanks for your feedback! ' . Str::random(10),
                'user_id' => $userIds->random(),
                'review_id' => $review_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
