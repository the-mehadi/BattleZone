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
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('map');
            $table->string('room_code')->nullable();
            $table->string('room_password')->nullable();
            $table->dateTime('match_time');
            $table->decimal('entry_fee', 10, 2);
            $table->decimal('total_prize', 10, 2);
            $table->boolean('is_room_locked')->default(true);
            $table->enum('status', ['upcoming', 'live', 'finished', 'cancelled'])->default('upcoming');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('category_id');
            $table->index('created_by');
            $table->index('match_time');
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
