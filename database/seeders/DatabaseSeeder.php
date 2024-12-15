<?php

namespace Database\Seeders;

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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        //作成したSeederを一気に実行させる
        // ./vendor/bin/sail php artisan migrate:refresh --seed
        $this->call([
            CardSeeder::class,
            DeckSeeder::class,
            DeckCardSeeder::class,
            UserSeeder::class,
            UserCardSeeder::class,
        ]);
    }

}
