<?php

namespace App\Http\Controllers;

use App\Models\UserCard;
use Illuminate\Http\Request;

class UserCardController extends Controller
{
    public function show(){
        //usercardsに紐づくcardsの情報を取得
        $user_cards=UserCard::with('card')->get();
    
        //JSON形式でデータを返す
         return response()->json($user_cards);
    }
    
}
