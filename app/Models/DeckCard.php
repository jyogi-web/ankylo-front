<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeckCard extends Model
{
    use HasFactory;

    // テーブル名が 'deck_cards' であるため、明示的な指定は不要です。

    protected $fillable = [
        'deck_id',
        'card_id',
        'quantity',
        'position', // カードの順序を管理するカラムを追加
    ];

    /**
     * デッキ
     */
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    /**
     * カード
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
