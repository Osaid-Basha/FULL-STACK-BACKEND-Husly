<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->pluck('id')->toArray();
        $statuses = ['sent', 'read', 'delivered'];

        foreach (range(1, 20) as $i) {
            $sender = $users[array_rand($users)];
            do {
                $receiver = $users[array_rand($users)];
            } while ($sender == $receiver);

            DB::table('messages')->insert([
                'status' => $statuses[array_rand($statuses)],
                'textContent' => 'Message content ' . Str::random(20),
                'user_sender_id' => $sender,
                'user_receiver_id' => $receiver,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
