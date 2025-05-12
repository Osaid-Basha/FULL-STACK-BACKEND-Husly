<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (range(1, 10) as $i) {
            DB::table('profiles')->insert([
                'imag_path' => Str::random(10),
                'facebook_url' => Str::random(10),
                'instagram_url' => Str::random(10),
                'twitter_url' => Str::random(10),
                'linkedin_url' => Str::random(10),
                'current_position' => Str::random(10),
                'phone' => Str::random(10),
                'location' => Str::random(10),
                'user_id' => $i,
            ]);
        }

    }
}
