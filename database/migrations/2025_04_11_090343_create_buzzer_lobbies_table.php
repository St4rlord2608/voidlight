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
        Schema::create('buzzer_lobbies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Lobby\Lobby::class)->constrained();
            $table->boolean('BuzzerLocked')->default(false);
            $table->string('BuzzedPlayerId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buzzer_lobbies');
    }
};
