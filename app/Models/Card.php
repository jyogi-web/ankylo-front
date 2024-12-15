<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    // テーブル名が 'cards' であるため、明示的な指定は不要です。

    protected $fillable = [
        'name',
        'description',
        'type',
        'power',
        'rank',
        // 'cost',        // 追加カラム例: カードのコスト
        // 'rarity',      // 追加カラム例: カードのレアリティ
        // 'image_url',   // 追加カラム例: カードの画像URL
        // 'effect',      // 追加カラム例: カードの効果をJSONで保存
    ];

    /**
     * デッキに含まれるデッキカード
     */
    public function deckCards()
    {
        return $this->hasMany(DeckCard::class);
    }

    /**
     * デッキに含まれるデッキを通じてデッキ
     */
    public function decks()
    {
        return $this->belongsToMany(Deck::class, 'deck_cards')->withPivot('quantity', 'position');
    }

    public function userCards()
    {
        return $this->belongsToMany(userCards::class);
    }

    public function users()
    {
        return $this->belogsToMany(User::class,'user_cards');
    }
}
