<?php

namespace App\Http\Controllers;

use App\Models\UserCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class UserCardController extends Controller
{
    public function show(){
        
        //usercardsに紐づくcardsの情報を取得
        $user_cards=UserCard::with('card')
        ->where('user_id',Auth::id())//ユーザIDでフィルタリング
        ->get();
    
        //JSON形式でデータを返す
         return response()->json($user_cards);
    }
}
