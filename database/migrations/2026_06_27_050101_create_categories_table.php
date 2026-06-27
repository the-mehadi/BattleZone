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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('squad_type', ['squad', 'duo', 'solo']);
            $table->unsignedTinyInteger('max_players');
            $table->string('map');
            $table->text('rules');
            $table->decimal('entry_fee', 10, 2);
            $table->decimal('kill_point', 5, 2);
            $table->unsignedInteger('match_duration');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('squad_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
