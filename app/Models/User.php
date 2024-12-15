<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ユーザーが作成したルーム
     */
    public function createdRooms()
    {
        return $this->hasMany(Room::class, 'created_by');
    }

    /**
     * ユーザーが参加しているルームメンバーシップ
     */
    public function roomMemberships()
    {
        return $this->hasMany(RoomMember::class);
    }

    /**
     * ユーザーがプレイヤー1として参加したマッチ
     */
    public function matchesAsPlayer1()
    {
        return $this->hasMany(MatchModel::class, 'player1_id');
    }

    /**
     * ユーザーがプレイヤー2として参加したマッチ
     */
    public function matchesAsPlayer2()
    {
        return $this->hasMany(MatchModel::class, 'player2_id');
    }

    /**
     * ユーザーが所有するデッキ
     */
    public function decks()
    {
        return $this->hasMany(Deck::class, 'owner_id');
    }

    public function user_cards()
    {
        return $this->belongsToMany(Card::class,UserCard::class);
    }

    public function cards()
    {
        // `cards` と `user_cards` の `id` の競合を避けるため、明示的にカラムを指定
        return $this->belongsToMany(Card::class, 'user_cards', 'user_id', 'card_id');
    }

}
