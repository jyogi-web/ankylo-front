<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchModel extends Model
{
    use HasFactory;

    // テーブル名が 'matches' であるため、明示的な指定は不要です。

    protected $fillable = [
        'room_id',
        'player1_id',
        'player2_id',
        'deck_id',
        'start_time',
        'end_time',
        'result',
        'current_turn', // 追加カラム（例: 現在のターン）
    ];

    /**
     * マッチが属するルーム
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * プレイヤー1
     */
    public function player1()
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    /**
     * プレイヤー2
     */
    public function player2()
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    /**
     * 使用されるデッキ
     */
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }
}
