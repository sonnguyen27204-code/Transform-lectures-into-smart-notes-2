<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
            $table->longText('full_text');
            $table->string('language', 10)->default('vi');
            $table->unsignedInteger('word_count')->default(0);
            $table->float('confidence')->nullable();
            $table->timestamps();
        });

        Schema::create('transcript_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcript_id')->constrained('transcripts')->cascadeOnDelete();
            $table->float('start_time');
            $table->float('end_time');
            $table->text('text');
            $table->string('speaker', 20)->default('unknown');
            $table->string('speaker_label')->nullable();
            $table->timestamps();

            $table->index(['transcript_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_segments');
        Schema::dropIfExists('transcripts');
    }
};