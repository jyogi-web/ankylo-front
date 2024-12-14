<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade'); // room_id INT FOREIGN KEY rooms.id NOT NULL
            $table->foreignId('player1_id')->constrained('users')->onDelete('cascade'); // player1_id INT FOREIGN KEY users.id NOT NULL
            $table->foreignId('player2_id')->constrained('users')->onDelete('cascade'); // player2_id INT FOREIGN KEY users.id NOT NULL
            $table->foreignId('deck_id')->constrained('decks')->onDelete('cascade'); // deck_id INT FOREIGN KEY decks.id NOT NULL
            $table->timestamp('start_time')->useCurrent(); // start_time DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->timestamp('end_time')->nullable(); // end_time DATETIME NULLABLE
            $table->enum('result', ['player1_win', 'player2_win', 'draw'])->nullable(); // result ENUM('player1_win', 'player2_win', 'draw') NULLABLE
            $table->integer('current_turn')->nullable(); // current_turn INT NULLABLE
            $table->timestamps(); // created_at と updated_at DATETIME
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};