<?php

namespace Database\Seeders;

use App\Models\listing_type;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       /*  User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
        $this->call([
    UserSeeder::class,
    ProfileSeeder::class,
    NotificationSeeder::class,
    NotificationUserSeeder::class,
    RoleSeeder::class,
    RoleUsersSeeder::class,
    MessageSeeder::class,
    PropertyTypeSeeder::class,
    ListingTypeSeeder::class,
    PurchaseSeeder::class,
    PropertySeeder::class,
    PropertyImageSeeder::class,
    AmenitySeeder::class,
    PropertyAmenitiesSeeder::class,
    FavoritesSeeder::class,
    NegotiationSeeder::class,
    NegotiationUserSeeder::class,
    BuyingRequestSeeder::class,
    ReviewSeeder::class,
    ReplaySeeder::class,




]);
    }
}
