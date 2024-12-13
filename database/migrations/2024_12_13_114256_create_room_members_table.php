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
        Schema::create('room_members', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade'); // room_id INT FOREIGN KEY rooms.id NOT NULL
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // user_id INT FOREIGN KEY users.id NOT NULL
            $table->enum('role', ['leader', 'participant'])->default('participant'); // role ENUM('leader', 'participant') DEFAULT 'participant' NOT NULL
            $table->timestamp('joined_at')->useCurrent(); // joined_at DATETIME DEFAULT CURRENT_TIMESTAMP
            $table->unique(['room_id', 'user_id']); // (room_id, user_id) UNIQUE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_members');
    }
};
