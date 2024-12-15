<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_turns', function (Blueprint $table) {
            $table->id(); // プライマリキー
            $table->unsignedBigInteger('room_id'); // ルームID
            $table->integer('turn'); // ターン番号
            $table->unsignedBigInteger('winner_user_id'); // 勝者のユーザーID
            $table->integer('power_difference'); // パワー差
            $table->timestamps(); // created_at, updated_at

            // 外部キー制約
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_turns');
    }
}
