<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('score_obtained');
            $table->integer('score_max');
            $table->float('percentage');
            $table->json('proctoring_metrics')->nullable();
            $table->json('evaluation')->nullable();
            $table->enum('status', ['monitoring', 'completed', 'flagged'])->default('completed');
            $table->timestamps();

            // Index para búsquedas rápidas
            $table->index(['exam_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
