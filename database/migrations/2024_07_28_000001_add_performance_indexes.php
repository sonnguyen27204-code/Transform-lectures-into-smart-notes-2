<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->index(['lecture_id', 'difficulty']);
        });

        Schema::table('flashcards', function (Blueprint $table) {
            $table->index('lecture_id');
        });

        Schema::table('processing_jobs', function (Blueprint $table) {
            $table->index(['lecture_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['lecture_id', 'difficulty']);
        });

        Schema::table('flashcards', function (Blueprint $table) {
            $table->dropIndex(['lecture_id']);
        });

        Schema::table('processing_jobs', function (Blueprint $table) {
            $table->dropIndex(['lecture_id', 'created_at']);
        });
    }
};