<?php

//コントローラー
use App\Http\Controllers\UserCardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth:sanctum')->get('/user', function () {
    return auth()->user();  // ユーザー情報を返す
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //ページ
    // HomeControllerのindexメソッドを呼び出す
    Route::get('/home', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('home');

    // Route::get('/home', function () {
    //     return Inertia::render('Home');
    // })->name('home');
    Route::get('/social', function () {
        return Inertia::render('Social');
    })->name('social');
    Route::get('/battle', function () {
        return Inertia::render('Battle');
    })->name('battle');
    Route::get('/cards', function () {
        return Inertia::render('Cards');
    })->name('cards');

    Route::get('api/cards',[UserCardController::class,'show']);
    Route::post('/draw-gacha', [PackController::class, 'draw'])->name('draw.pack');


    //マッチング関係
    Route::post('/match', [MatchingController::class, 'match']);

    //ルーム
    Route::post('/room/create', [RoomController::class, 'create']);
    Route::post('/room/join', [RoomController::class, 'join']);
    Route::get('/room/{room}', [RoomController::class, 'show'])->name('battle.room');
    Route::get('/api/room/{room}/users', [RoomController::class, 'getUsers']);


});

require __DIR__.'/auth.php';
