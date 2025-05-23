<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegotiationUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Attach users to negotiations
        foreach (range(1, 10) as $i) {
            DB::table('negotiation_user')->insert([
                'user_id' => $i,
                'negotiation_id' => rand(1, 10), // Assuming you have 10 negotiations
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
