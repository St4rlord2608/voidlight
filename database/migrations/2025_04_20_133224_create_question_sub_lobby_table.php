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
        Schema::create('question_sub_lobby', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Question\Question::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Lobby\SubLobby::class)->constrained()->onDelete('cascade');
            $table->unique(['sub_lobby_id', 'question_id'], 'question_sub_lobby_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_sub_lobby');
    }
};
