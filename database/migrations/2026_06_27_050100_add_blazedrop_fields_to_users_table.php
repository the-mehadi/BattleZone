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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->after('email');
            $table->enum('role', ['moderator', 'player'])->default('player')->after('phone');
            $table->decimal('wallet_balance', 10, 2)->default(0)->after('role');
            $table->boolean('is_banned')->default(false)->after('wallet_balance');

            $table->index('role');
            $table->index('is_banned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_banned']);
            $table->dropColumn(['phone', 'role', 'wallet_balance', 'is_banned']);
        });
    }
};
