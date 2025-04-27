<?php

use App\Models\Jeopardy\JeopardyLobby;
use App\Models\Question\Category;
use App\Models\Question\Question;
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
        Schema::create('jeopardy_board_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(JeopardyLobby::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Category::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Question::class)->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->boolean('answered')->default(false);
            $table->timestamps();

            $table->unique(['jeopardy_lobby_id', 'category_id', 'question_id'], 'jeopardy_board_cells_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jeopardy_board_cells');
    }
};
