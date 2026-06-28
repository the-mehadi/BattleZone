<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_squads')->default(52)->after('kill_prize_per_kill');
        });

        DB::statement("
            UPDATE rooms
            INNER JOIN categories ON categories.id = rooms.category_id
            SET rooms.max_squads = CASE categories.squad_type
                WHEN 'squad' THEN 13
                WHEN 'duo' THEN 26
                ELSE 52
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('max_squads');
        });
    }
};
