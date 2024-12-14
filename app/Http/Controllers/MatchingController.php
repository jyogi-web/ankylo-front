<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    public function match()
    {
        try {
            $user = Auth::user();
            Log::info('User authenticated', ['user_id' => $user->id]);

            // waiting状態のroomを探す
            $room = Room::where('status', 'waiting')->first();
            Log::info('Waiting room found', ['room' => $room]);

            if ($room) {
                // roomにユーザーを追加
                RoomMember::create([
                    'room_id' => $room->id,
                    'user_id' => $user->id,
                    'role' => 'participant',
                ]);

                // roomのメンバー数を確認
                $memberCount = RoomMember::where('room_id', $room->id)->count();
                Log::info('Member count', ['count' => $memberCount]);

                if ($memberCount >= 2) {
                    $room->status = 'in_game'; // 2人揃ったらステータスをin_gameに変更
                    $room->save();
                }
            } else {
                // waiting状態のroomがない場合、新しいroomを作成
                $room = Room::create([
                    'name' => 'Room ' . uniqid(),
                    'created_by' => $user->id,
                    'status' => 'waiting',
                ]);

                // roomにユーザーを追加
                RoomMember::create([
                    'room_id' => $room->id,
                    'user_id' => $user->id,
                    'role' => 'leader',
                ]);
            }

            return response()->json(['room' => $room]);
        } catch (\Exception $e) {
            Log::error('Error during matching', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

}