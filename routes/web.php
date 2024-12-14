<?php

//コントローラー
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\DeckController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //ページ
    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');
    Route::get('/social', function () {
        return Inertia::render('Social');
    })->name('social');
    Route::get('/battle', function () {
        return Inertia::render('Battle');
    })->name('battle');
    Route::get('/cards', function () {
        return Inertia::render('Cards');
    })->name('cards');

    //マッチング関係
    Route::post('/match', [MatchingController::class, 'match']);

    //ルーム
    Route::post('/room/create', [RoomController::class, 'create']);
    Route::post('/room/join', [RoomController::class, 'join']);
    Route::get('/room/{room}', [RoomController::class, 'show'])->name('battle.room');
    Route::get('/api/room/{room}/users', [RoomController::class, 'getUsers']);
    Route::post('/api/room/{roomId}/select-card', [RoomController::class, 'selectCard']);
    
    Route::get('/api/decks/{deck}/cards', [DeckController::class, 'getCards']);
    // 全員がカードを選択したか確認するルート
    Route::get('/api/room/{roomId}/check-all-selected', [RoomController::class, 'checkAllSelected']);
    Route::post('/api/room/{roomId}/judge', [RoomController::class, 'judge']);
    Route::post('/api/room/{roomId}/selectCard',[RoomController::class, 'selectCard']);
});

require __DIR__.'/auth.php';
