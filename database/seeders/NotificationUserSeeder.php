<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (range(1, 10) as $i) {
            DB::table('notification_user')->insert([
                'user_id' => rand(1, 10),
                'notification_id' => rand(1, 10),

            ]);
        }
    }
}
