<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuyingRequestSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id');
        $propertyIds = DB::table('properties')->pluck('id');

        $types = ['Direct Purchase', 'Financing', 'Installment'];
        $statuses = [0, 1]; // 0 = pending/rejected, 1 = accepted

        foreach (range(1, 10) as $i) {
            DB::table('buying_requests')->insert([
                'status' => $statuses[array_rand($statuses)],
                'type' => $types[array_rand($types)],
                'user_id' => $userIds->random(),
                'property_id' => $propertyIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
