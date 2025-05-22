<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['purchase', 'message', 'comment'];

        foreach (range(1, 10) as $i) {
            DB::table('notifications')->insert([
                'type' => $types[array_rand($types)],
                'message_content' => 'Notification message ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
