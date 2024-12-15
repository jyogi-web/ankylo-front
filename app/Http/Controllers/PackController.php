<?php

namespace App\Http\Controllers;

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

        // ダミーの結果
        $reward = ['name' => 'レアカード', 'rarity' => 'SSR'];

        return response()->json([
            'reward' => $reward,
            'available_pack_draws'=>$user->available_pack_draws,
        ]);
    }

}
