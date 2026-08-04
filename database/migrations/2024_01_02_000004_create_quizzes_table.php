<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
            $table->text('question');
            $table->text('explanation')->nullable();
            $table->string('difficulty', 20)->default('medium');
            $table->string('topic')->nullable();
            $table->unsignedTinyInteger('correct_index')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->text('text');
            $table->unsignedTinyInteger('index');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index(['quiz_id', 'index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quizzes');
    }
};