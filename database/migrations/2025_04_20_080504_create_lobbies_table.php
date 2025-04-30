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
        Schema::create('lobbies', function (Blueprint $table) {
            $table->id();
            $table->string('host_id');
            $table->string('lobby_code')->unique();
            $table->foreignIdFor(\App\Models\Lobby\SubLobby::class)->constrained();
            $table->timestamps();
            $table->index('sub_lobby_id');
            $table->index('host_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lobbies');
    }
};
