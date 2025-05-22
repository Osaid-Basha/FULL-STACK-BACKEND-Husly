<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            DB::table('profiles')->insert([
                'imag_path' => 'https://via.placeholder.com/150',
                'facebook_url' => 'https://facebook.com/' . Str::slug($user->first_name . $user->last_name),
                'instagram_url' => 'https://instagram.com/' . Str::slug($user->first_name),
                'twitter_url' => 'https://twitter.com/' . Str::slug($user->first_name),
                'linkedin_url' => 'https://linkedin.com/in/' . Str::slug($user->first_name . '-' . $user->last_name),
                'current_position' => 'Agent',
                'phone' => '059' . rand(1000000, 9999999),
                'location' => 'Nablus',
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
