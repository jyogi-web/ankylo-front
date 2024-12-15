<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // 新しいカラムを追加
            $table->dateTime('last_pack_drawn_at')->nullable()->after('updated_at');
            $table->unsignedInteger('available_pack_draws')->default(10)->after('last_pack_drawn_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // 新しいカラムを削除
            if (Schema::hasColumn('users', 'last_pack_drawn_at')) {
                $table->dropColumn('last_pack_drawn_at');
            }
            if (Schema::hasColumn('users', 'available_pack_draws')) {
                $table->dropColumn('available_pack_draws');
            }
        });
    }
};
