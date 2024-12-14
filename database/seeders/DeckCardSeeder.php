<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\DeckCard;
use Illuminate\Support\Facades\Hash;

class DeckCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DeckCard:create([
            'deck_id'=>'',
            'quantity'
        ])
    }
}
