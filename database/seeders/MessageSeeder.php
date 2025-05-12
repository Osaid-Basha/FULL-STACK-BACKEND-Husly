<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (range(1, 10) as $i) {

            DB::table('messages')->insert([
                'user_sender_id' => rand(1, 10),
                'user_receiver_id' => rand(1, 10),
                'textContent' => Str::random(10),
                'status' => Str::random(10),

            ]);
        }
    }
}
