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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('squad_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position');
            $table->unsignedInteger('total_kills')->default(0);
            $table->decimal('kill_point', 8, 2)->default(0);
            $table->decimal('rank_point', 8, 2)->default(0);
            $table->decimal('total_point', 8, 2)->default(0);
            $table->decimal('prize_won', 10, 2)->default(0);
            $table->timestamps();

            $table->index('room_id');
            $table->index('squad_id');
            $table->unique(['room_id', 'squad_id']);
            $table->unique(['room_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
