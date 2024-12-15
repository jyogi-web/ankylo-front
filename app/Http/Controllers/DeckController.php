<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deck;
use App\Models\DeckCard;
use Illuminate\Support\Facades\Response;

class DeckController extends Controller
{
    public function getCards($deckId)
    {
        $cards = DeckCard::where('deck_id', $deckId)
            ->join('cards', 'deck_cards.card_id', '=', 'cards.id')
            ->select('deck_cards.card_id', 'cards.name', 'cards.type', 'cards.power')
            ->get();

        return Response::json($cards);
    }
}
