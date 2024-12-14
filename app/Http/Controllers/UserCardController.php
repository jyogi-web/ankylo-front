<?php

namespace App\Http\Controllers;

use App\Models\UserCard;
use Illuminate\Http\Request;

class UserCardController extends Controller
{
    public function show(){
        //データベースからデータ取得
    $user_cards=UserCard::all();
    return response()->json($user_cards);
    }
    
}
