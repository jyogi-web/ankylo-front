<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    // テーブル名が 'rooms' であるため、明示的な指定は不要です。

    protected $fillable = [
        'name',
        'created_by',
        'status',
    ];

    /**
     * ルームを作成したユーザー
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ルームのメンバー
     */
    public function members()
    {
        return $this->hasMany(RoomMember::class);
    }

    /**
     * ルーム内のマッチ
     */
    public function matches()
    {
        return $this->hasMany(MatchModel::class);
    }
}
