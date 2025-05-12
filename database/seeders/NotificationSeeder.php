<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (range(1, 10) as $i) {
            DB::table('notifications')->insert([
                'type' => Str::random(10),
                'message_content' => Str::random(10),
                'status' => Str::random(10),
                'read_at' => now(),
            ]);
        }



    }
}
