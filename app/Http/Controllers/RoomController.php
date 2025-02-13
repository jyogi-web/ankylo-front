<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomMember;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use App\Models\DeckCard;
use App\Models\MatchTurn;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    //
    /**
     * ルーム作成
     */
    public function create(Request $request)
    {
        $userId = Auth::id();

        $room = new Room();
        $room->name = 'Room' . $room->id;
        $room->created_by = $userId;
        $room->status = 'waiting';
        $room->save();

        $room->name = 'Room' . $room->id;
        $room->save();

        $roomMember = new RoomMember();
        $roomMember->room_id = $room->id;
        $roomMember->user_id = $userId;
        $roomMember->save();

        return Inertia::location(route('battle.room', ['room' => $room->id]));
    }

    /**
     * ルーム参加
     * roomId指定
     */
    public function join(Request $request)
    {
        try {
            $userId = Auth::id();
            $roomId = $request->input('room_id');

            $room = Room::findOrFail($roomId);

            // 既に他のルームに参加している場合、その情報を削除
            RoomMember::where('user_id', $userId)->delete();
            
            //roomのステートを確認してin_gameだったらbattleに戻る
            if($room->status == 'in_game'){
                return Inertia::location(route('battle'));
            }


            // DeckCardsテーブルのpositionカラムをnullで初期化する
            DB::table('deck_cards')
            ->where('deck_id', $userId)
            ->update(['position' => null]);

            $roomMember = new RoomMember();
            $roomMember->room_id = $roomId;
            $roomMember->user_id = $userId;
            $roomMember->save();

            // ルームの人数を確認し、2人以上ならステータスを'in_game'に変更
            $memberCount = RoomMember::where('room_id', $roomId)->count();
            if ($memberCount >= 2) {
                $room->status = 'in_game';
                $room->turn = 1;    // ルームのターンを初期化
                $room->save();
            }

            \Log::error('joining room: ' . $roomId);

            return Inertia::location(route('battle.room', ['room' => $roomId]));
        } catch (\Exception $e) {
            \Log::error('Error joining room: ' . $e->getMessage());
            return Inertia::render('Battle');
        }
    }

    public function show($roomId)
    {
        $room = Room::findOrFail($roomId);
        $users = User::whereIn('id', RoomMember::where('room_id', $roomId)->pluck('user_id'))->get();
        $user_id = Auth::id(); // ログインしているユーザーIDを取得

        return Inertia::render('room/BattleRoom', [
            'room' => $room,
            'user_id' => $user_id,
            'users' => $users,
        ]);
    }

    public function getUsers($roomId)
    {
        $users = User::whereIn('id', RoomMember::where('room_id', $roomId)->pluck('user_id'))->get();
        return Response::json($users);
    }

    public function selectCard(Request $request, $roomId)
    {
        \Log::info('Updating has_selected_card for user');
        $userId = auth()->id();
        $roomId = (int) $roomId;
        $cardId = $request->input('card_id');
        $turn = $request->input('turn');

        \Log::info('Updating has_selected_card for user', [
            'card_id' => $cardId,
            'room_id' => $roomId,
            'user_id' => $userId,
            'turn' => $turn,
        ]);

        // カードを選んだターンを保存
        DB::table('deck_cards')
            ->where('deck_id', $userId)
            ->where('card_id', $cardId)
            ->update(['position' => $turn]);

        // カード選択ロジック（必要に応じてカードのチェックなどを実施）
        // データベースにカード選択状況を保存
        DB::table('room_members')
            ->where('room_id', $roomId)
            ->where('user_id', $userId)
            ->update(['has_selected_card' => true]);
        return response()->json(['success' => true]);
    }

    public function judge(Request $request, $roomId)
    {
        //　roomId がString
        $turn = DB::table('rooms')->where('id', $roomId)->value('turn');
        $userId = auth()->id();
        
        \Log::info('Updating has_selected_card for user', [
            'turn' => $turn,
            'room_id' => $roomId,
            'user_id' => $userId,
        ]);
        
        DB::table('room_members')
            ->where('room_id', $roomId)
            ->update(['has_selected_card' => false]);

        \Log::info('カード選択更新', [
            'position' => $turn,
        ]);
            
        // 現在のターンに出されている2つのカードを取得
        $cards = DB::table('deck_cards')
            ->join('cards', 'deck_cards.card_id', '=', 'cards.id')
            ->where('deck_cards.position', $turn)
            ->select('deck_cards.deck_id', 'cards.id as card_id', 'cards.power')
            ->get();

        \Log::info('カード取得後',['cards',$cards]);
        // [2024-12-14 16:40:52] local.INFO: カード取得後 ["cards",{"Illuminate\\Support\\Collection":[]}] 

        // カード数が不足している場合のエラーハンドリング
        if ($cards->count() < 2) {
            \Log::error('必要なカードが不足しています。現在のカード数:', ['count' => $cards->count()]);
            return response()->json(['error' => '必要なカードが不足しています'], 400);
        }

        $card1 = $cards[0];
        $card2 = $cards[1];
        \Log::info(['card1'=>$card1,'card2'=>$card2]);

        \Log::info('判定前まで');

        // 勝者を判定
        $winner = null;
        $powerDifference = abs($card1->power - $card2->power); // パワー差を計算
        if($card1->power == $card2->power){
            $winner = null;
        } elseif ($card1->power > $card2->power) {
            $winner = User::find($card1->deck_id);
        } elseif ($card1->power < $card2->power) {
            $winner = User::find($card2->deck_id);
        }

        \Log::info(['room_id' => $roomId,
        'turn' => $turn,
        'winner_user_id' => $winner->id,
        'power_difference' => $powerDifference]);

        // ターン結果を保存
        MatchTurn::create([
            'room_id' => $roomId,
            'turn' => $turn,
            'winner_user_id' => $winner->id,
            'power_difference' => $powerDifference,
        ]);
        
        if ($winner) {
            DB::table('rooms')
                ->where('id', $roomId)
                ->update(['winner_user_id' => $winner->id]);
    
            \Log::info('勝者保存', ['winner' => $winner->id]);
        } else {
            DB::table('rooms')
                ->where('id', $roomId)
                ->update(['winner_user_id' => null]);
            \Log::info('引き分け');
        }  

        // ターンを1つ増やし、カード選択状態をリセット
        $room = Room::findOrFail($roomId);
        $room->turn += 1;
        $room->save();


        \Log::info('judge終了');        
        
        return response()->json(['winner' => $winner]);
    }
    

    //全員選択しているかを確認するAPI
    public function checkAllSelected($roomId)
    {
        $roomExists = DB::table('room_members')->where('room_id', $roomId)->exists();

        if (!$roomExists) {
            return response()->json(['allSelected' => false]);
        }

        $allSelected = DB::table('room_members')
            ->where('room_id', $roomId)
            ->where('has_selected_card', true)
            ->count() == DB::table('room_members')->where('room_id', $roomId)->count();

        $created_by = DB::table('rooms')->where('id', $roomId)->value('created_by');
        $winner = DB::table('rooms')->where('id', $roomId)->value('winner_user_id');
        $turn = DB::table('rooms')->where('id', $roomId)->value('turn');

        return response()->json(['allSelected' => $allSelected,'created_by' => $created_by,'winner' => $winner,'turn' => $turn]);
    }

    //リザルトを取得
    public function getTurnHistory($roomId)
    {
        // 指定されたroom_idのターン履歴を取得
        $turns = MatchTurn::where('room_id', $roomId)
            ->orderBy('turn', 'asc')
            ->get();

        // 勝者ごとのpowerDifferenceの合計を計算
        $winnerStats = $turns->groupBy('winner_user_id')
            ->map(function ($group) {
                return [
                    'total_power_difference' => $group->sum('power_difference'), // 合計のパワー差
                    'win_count' => $group->count(), // 勝利回数
                ];
            });
        \Log::info([
            'turns' => $turns, // 全ターンの履歴
            'winner_stats' => $winnerStats, // 勝者ごとの統計情報
        ]);

        // 結果を整形して返す
        return response()->json([
            'turns' => $turns, // 全ターンの履歴
            'winner_stats' => $winnerStats, // 勝者ごとの統計情報
        ]);
    }

}
