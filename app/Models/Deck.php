<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deck extends Model
{
    use HasFactory;

    // テーブル名が 'decks' であるため、明示的な指定は不要です。

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'is_public', // 公開設定カラムを追加
    ];

    /**
     * デッキの所有者
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * デッキに含まれるカード
     */
    public function deckCards()
    {
        return $this->hasMany(DeckCard::class);
    }

    /**
     * デッキに含まれるカード（カードモデルを直接取得）
     */
    public function cards()
    {
        return $this->belongsToMany(Card::class, 'deck_cards')->withPivot('quantity', 'position');
    }

    /**
     * デッキを使用したマッチ
     */
    public function matches()
    {
        return $this->hasMany(MatchModel::class);
    }
}
