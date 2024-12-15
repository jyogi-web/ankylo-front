<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchTurn extends Model
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'match_turns';

    /**
     * 許可するカラムの定義 (Mass Assignment対応)
     *
     * @var array
     */
    protected $fillable = [
        'room_id',          // ルームID
        'turn',             // ターン番号
        'winner_user_id',   // 勝者のユーザーID
        'power_difference', // パワー差
    ];

    /**
     * Roomとのリレーション
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Winnerとのリレーション (User)
     */
    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }
}
