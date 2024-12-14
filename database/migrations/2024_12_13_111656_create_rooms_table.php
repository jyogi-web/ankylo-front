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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->string('name', 100)->unique(); // name VARCHAR(100) UNIQUE NOT NULL
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // created_by INT FOREIGN KEY users.id NOT NULL
            $table->enum('status', ['waiting', 'in_game', 'closed'])->default('waiting'); // status ENUM('waiting', 'in_game', 'closed') DEFAULT 'waiting' NOT NULL
            $table->integer('turn')->nullable(); // turn INT NULLABLE
            $table->timestamps(); // created_at と updated_at DATETIME
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
