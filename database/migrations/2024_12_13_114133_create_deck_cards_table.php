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
        Schema::create('deck_cards', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('deck_id')->constrained('decks')->onDelete('cascade'); // deck_id INT FOREIGN KEY decks.id NOT NULL
            $table->foreignId('card_id')->constrained('cards')->onDelete('cascade'); // card_id INT FOREIGN KEY cards.id NOT NULL
            $table->integer('quantity')->default(1); // quantity INT DEFAULT 1 NOT NULL
            $table->integer('position')->nullable(); // position INT NULLABLE
            $table->timestamps(); // created_at と updated_at DATETIME

            $table->unique(['deck_id', 'card_id']); // (deck_id, card_id) UNIQUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deck_cards');
    }
};