<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
            $table->text('brief');
            $table->json('key_takeaways');
            $table->json('topics')->nullable();
            $table->string('sentiment', 20)->default('neutral');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};