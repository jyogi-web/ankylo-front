<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomMember;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
            \Log::error('joining room: ' . $roomId);

            return Inertia::location(route('battle.room', ['room' => $roomId]));
        } catch (\Exception $e) {
            \Log::error('Error joining room: ' . $e->getMessage());
            return Inertia::render('Battle');
        }
    }

}
