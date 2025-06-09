<?php

namespace Database\Seeders;

use App\Models\listing_type;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{public function run(): void
{
    $this->call([
        // الجداول الأساسية المستقلة
        RoleSeeder::class,
        AmenitySeeder::class,
        PropertyTypeSeeder::class,
        ListingTypeSeeder::class,


        UserSeeder::class,


       


        PropertySeeder::class,


        PropertyImageSeeder::class,


        PropertyAmenitiesSeeder::class,


        BuyingRequestSeeder::class,

        ReviewSeeder::class,


        ReplaySeeder::class,


        NotificationSeeder::class,


        NotificationUserSeeder::class,


        MessageSeeder::class,


        NegotiationSeeder::class,


        FavoritesSeeder::class,


        ProfileSeeder::class,
    ]);
}

}
