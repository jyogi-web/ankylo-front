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

        return $room;
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
        $turn = $request->input('turn');
        $userId = auth()->id();

        \Log::info('Updating has_selected_card for user', [
            'turn' => $turn,
            'room_id' => $roomId,
            'user_id' => $userId,
        ]);
        
        // 現在のターンに出されている2つのカードを取得
        $cards = DB::table('deck_cards')
            ->join('cards', 'deck_cards.card_id', '=', 'cards.id')
            ->where('deck_cards.position', $turn)
            ->select('deck_cards.deck_id', 'cards.id as card_id', 'cards.power')
            ->get();

        \Log::info('カード取得後',['cards',$cards]);
        // [2024-12-14 16:40:52] local.INFO: カード取得後 ["cards",{"Illuminate\\Support\\Collection":[]}] 


        if ($cards->count() < 2) {
            return response()->json(['error' => 'Insufficient cards for judgment'], 400);
        }

        $card1 = $cards[0];
        $card2 = $cards[1];

        \Log::info('判定前まで');

        // 勝者を判定
        $winner = null;
        if($card1->power == $card2->power){
            $winner = null;
        } elseif ($card1->power > $card2->power) {
            $winner = User::find($card1->deck_id);
        } elseif ($card1->power < $card2->power) {
            $winner = User::find($card2->deck_id);
        }
        
        DB::table('rooms')
            ->where('id', $roomId)
            ->update(['winner_user_id' => $winner->id]);

        \Log::info('勝者保存',['winner',$winner->id]);        

        // ターンを1つ増やし、カード選択状態をリセット
        $room = Room::findOrFail($roomId);
        $room->turn += 1;
        $room->save();

        DB::table('room_members')
            ->where('room_id', $roomId)
            ->update(['has_selected_card' => false]);

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


        return response()->json(['allSelected' => $allSelected,'created_by' => $created_by,'winner' => $winner]);
    }

}
