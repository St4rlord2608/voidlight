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
        Schema::create('question_categories_pivot', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Question\Question::class);
            $table->foreignIdFor(\App\Models\Question\Category::class);
            $table->unique(['question_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_categories_pivot');
    }
};
