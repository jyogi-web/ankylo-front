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
        //サンプルデッキ
        DeckCard::create([
            'deck_id'=>1,
            'position'=>null,
            'card_id'=>33,
        ]);
        DeckCard::create([
            'deck_id'=>1,
            'position'=>null,
            'card_id'=>77,
        ]);
        DeckCard::create([
            'deck_id'=>1,
            'position'=>null,
            'card_id'=>93,
        ]);
        DeckCard::create([
            'deck_id'=>1,
            'position'=>null,
            'card_id'=>112,
        ]);
        DeckCard::create([
            'deck_id'=>1,
            'position'=>null,
            'card_id'=>2,
        ]);

        //サンプルデッキ2
        DeckCard::create([
            'deck_id'=>2,
            'position'=>null,
            'card_id'=>34,
        ]);
        DeckCard::create([
            'deck_id'=>2,
            'position'=>null,
            'card_id'=>78,
        ]);
        DeckCard::create([
            'deck_id'=>2,
            'position'=>null,
            'card_id'=>94,
        ]);
        DeckCard::create([
            'deck_id'=>2,
            'position'=>null,
            'card_id'=>113,
        ]);
        DeckCard::create([
            'deck_id'=>2,
            'position'=>null,
            'card_id'=>3,
        ]);
    }
}
