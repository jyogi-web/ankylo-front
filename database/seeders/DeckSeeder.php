<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Deck;
use Illuminate\Support\Facades\Hash;

class DeckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Deck::create([
            'name'=>'サンプルデッキ',
            'owner_id'=>1
        ]);
    }
}
