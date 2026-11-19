<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('result', ['win', 'loss']);
            $table->enum('win_type', ['single', 'gammon', 'backgammon'])->default('single');
            $table->integer('points'); // +1/+2/+3 for wins, negative for losses
            $table->unsignedInteger('moves')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
