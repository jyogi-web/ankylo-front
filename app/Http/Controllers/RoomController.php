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
        $user_id = Auth::id(); // ログインしているユーザーID���取得

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
        $cardId = $request->input('card_id');
        $turn = $request->input('turn');

        \Log::info('Updating has_selected_card for user', [
            'card_id' => $cardId,
            'room_id' => $roomId,
            'user_id' => $userId,
            'turn' => $turn,
        ]);
    
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
        // todo : selectedCardがNULL
        //　roomId がString
        $turn = $request->input('turn');
        $selectedCard = $request->input('card');
        $userId = auth()->id();

        \Log::info('Updating has_selected_card for user', [
            'turn' => $turn,
            'selectedCard' => $selectedCard,
            'room_id' => $roomId,
            'user_id' => $userId,
        ]);
        
    
        // データベースにカード選択状況を保存
        // DB::table('room_members')
        //     ->where('room_id', $roomId)
        //     ->where('user_id', $userId)
        //     ->update(['has_selected_card' => true]);
        
        // 他のプレイヤーも選択済みか確認
        $allSelected = DB::table('room_members')
            ->where('room_id', $roomId)
            ->whereNull('has_selected_card')
            ->doesntExist();
        
        // どのカードを選んだかの順番を保存
        DB::table('deck_cards')
            ->where('deck_id',$userId)
            ->update(['position' => $turn]);


        if ($allSelected) {
            // 現在のターンに出されている2つのカードを取得
            $cards = DB::table('deck_cards')
                ->join('cards', 'deck_cards.card_id', '=', 'cards.id')
                ->where('deck_cards.position', $turn)
                ->select('deck_cards.deck_id', 'cards.id as card_id', 'cards.power')
                ->get();
    
            if ($cards->count() < 2) {
                return response()->json(['error' => 'Insufficient cards for judgment'], 400);
            }

            $card1 = $cards[0];
            $card2 = $cards[1];
    
            // 勝者を判定
            $winner = null;
            if($card1->power == $card2->power){
                $winner = 'Draw';
            } elseif ($card1->power > $card2->power) {
                $winner = User::find($card1->deck_id)->name;
            } elseif ($card1->power < $card2->power) {
                $winner = User::find($card2->deck_id)->name;
            }
    
            // ターンを1つ増やし、カード選択状態をリセット
            $room = Room::findOrFail($roomId);
            $room->turn += 1;
            $room->save();
    
            DB::table('room_members')
                ->where('room_id', $roomId)
                ->update(['has_selected_card' => false]);
    
            return response()->json(['winner' => $winner]);
        }
    
        return response()->json(['message' => 'Waiting for both players to select cards']);
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

        return response()->json(['allSelected' => $allSelected]);
    }

}
