<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomMember extends Model
{
    use HasFactory;

    // テーブル名が 'room_members' であるため、明示的な指定は不要です。

    protected $fillable = [
        'room_id',
        'user_id',
        'role', // 役割カラム（例: 'leader', 'participant'）を追加
    ];

    public $timestamps = false; 

    /**
     * ルームへのメンバーシップ
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * メンバーのユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
