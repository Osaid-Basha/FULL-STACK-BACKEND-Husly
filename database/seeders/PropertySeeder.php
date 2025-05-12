<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        foreach (range(1, 10) as $i) {


            DB::table('properties')->insert([
               'address' => Str::random(10),
               'city' => Str::random(10),
               'title'=> Str::random(10),
               'landArea' => rand(1, 100),
               'price' => rand(1, 100000),
               'bedroom' => rand(1, 10),
               'bathroom' => rand(1, 10),
               'parking' => rand(1, 10),
               'longDescreption' => Str::random(10),
                'shortDescreption' => Str::random(10),
                'constructionArea' => rand(1, 100),
                'livingArea' => rand(1, 100),
                'property_type_id' => rand(1, 10),
                'property_listing_id' => rand(1, 2),
                'user_id' => rand(1, 10),
                'purchase_id' => $i,
            ]);
        }
    }
}
