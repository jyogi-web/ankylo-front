<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\UserCard;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PackController extends Controller
{
    //


    public function draw()
    {
        $user = auth()->user();

        if ($user->available_pack_draws <= 0) {
            return response()->json(['message' => 'ガチャを引ける回数がありません'], 403);
        }

        // ガチャの処理
        $user->decrement('available_pack_draws');
        $user->update(['last_pack_drawn_at' => now()]);

        //ユーザーが所有していないカードを取得
        $ownedCardIds = $user->cards()->pluck('cards.id')->toArray();

        //ランダム五枚選択
        $cards=Card::whereNotIn('id',$ownedCardIds)->inRandomOrder()->limit(5)->get();

        //選ばれたカードをuser_cardsに登録
        foreach($cards as $card){
            UserCard::create([
                'user_id'=>$user->id,
                'card_id'=>$card->id,
            ]);
        }
        // ダミーの結果
        //$reward = ['name' => 'レアカード', 'rarity' => 'SSR'];

        return response()->json([
            // 'reward' => $reward,
            'available_pack_draws'=>$user->available_pack_draws,
            'cards'=>$cards,
        ]);
    }

}
