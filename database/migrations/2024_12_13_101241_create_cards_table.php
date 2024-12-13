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
        Schema::create('cards', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->string('name', 100)->unique(); // name VARCHAR(100) UNIQUE NOT NULL
            $table->text('description')->nullable(); // description TEXT NULLABLE
            $table->string('type', 50); // type VARCHAR(50) NOT NULL
            $table->integer('power')->nullable(); // power INT NULLABLE
            $table->string('rank', 50)->nullable(); // rank VARCHAR(50) NULLABLE
            $table->timestamps(); // created_at と updated_at DATETIME
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
