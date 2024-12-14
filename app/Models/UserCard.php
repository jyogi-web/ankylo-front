<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCard extends Model
{
    protected $table='user_cards';
    protected $fillable=['user_id','card_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function card():BelongsTo
    {
        return $this->belongsTo(Card::class,'card_id','id');
    }
}