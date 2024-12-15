<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class HomeController extends Controller
{
    // ガチャの間隔（時間単位）
    private $gacha_interval = 0.5; // 12時間間隔でガチャを引けるように設定

    public function index()
    {
        $user = auth()->user();

        // 引ける回数を更新
        $this->updatePackDrawCount($user);

        // 最後にガチャを引いた時刻を取得
        $lastDrawn = $user->last_pack_drawn_at;
        $now = now();

        // 次回ガチャまでの待機時間を計算
        $hoursRemaining = 0;
        if ($user->available_pack_draws < 2) {
            // ガチャが引ける回数が2回未満の場合、次回のガチャまでの待機時間を計算
            $hoursRemaining = $this->gacha_interval + $now->diffInHours($lastDrawn);
        }

        // 次回ガチャまでの時間が経過している場合、回数を更新
        if ($hoursRemaining <= 0) {
            $this->updatePackDrawCount($user); // ガチャ回数を更新
            $hoursRemaining = 0; // 経過しているため、待機時間は0
        }

        return inertia('Home', [
            'user' => $user,
            'hoursRemaining' => $hoursRemaining, // 次のガチャまでの待機時間
        ]);
    }

    protected function updatePackDrawCount($user)
    {
        // 最初にパックを引いた時間が設定されていない場合
        if (!$user->last_pack_drawn_at) {
            // 初回のパック引きなので、引ける回数を2回、最後に引いた時間を現在時刻に設定
            $user->update(['available_pack_draws' => 2, 'last_pack_drawn_at' => now()]);
            return; // 処理終了
        }

        // 最後にパックを引いた時間をCarbonオブジェクトとして取得
        $lastDrawn = Carbon::parse($user->last_pack_drawn_at);
        $now = now();

        // 経過時間を計算（時間単位で差を計算）
        $hoursPassed = $lastDrawn->diffInHours($now);

        // 12時間経過していれば、引ける回数を増やす
        if ($hoursPassed >= $this->gacha_interval) {
            // 経過時間に基づいて引ける回数を計算
            $additionalDraws = min(2, floor($hoursPassed / $this->gacha_interval));

            // 新しい引ける回数を計算（最大でも2回）
            $newDrawCount = min(2, $user->available_pack_draws + $additionalDraws);

            // 引ける回数を更新し、もし2回になった場合は最後に引いた時間も現在時刻に更新
            $user->update([
                'available_pack_draws' => $newDrawCount,
                // 引ける回数が2回になった場合のみ、最後に引いた時間を更新
                'last_pack_drawn_at' => $newDrawCount == 2 ? now() : $user->last_pack_drawn_at,
            ]);
        }
    }
}
