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
        Schema::create('decks', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->string('name', 100)->unique(); // name VARCHAR(100) UNIQUE NOT NULL
            $table->text('description')->nullable(); // description TEXT NULLABLE
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // owner_id INT FOREIGN KEY users.id NOT NULL
            $table->boolean('is_public')->default(true); // is_public BOOLEAN DEFAULT TRUE NOT NULL
            $table->timestamps(); // created_at と updated_at DATETIME
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decks');
    }
};
