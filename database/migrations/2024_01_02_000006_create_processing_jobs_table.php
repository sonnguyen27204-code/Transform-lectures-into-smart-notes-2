<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processing_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
            $table->string('stage', 30)->default('uploading');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['lecture_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processing_jobs');
    }
};