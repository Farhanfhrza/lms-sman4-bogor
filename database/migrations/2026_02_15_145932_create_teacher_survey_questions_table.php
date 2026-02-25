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
        Schema::create('teacher_survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('teacher_survey_periods')->cascadeOnDelete();
            $table->string('question_text');
            $table->integer('order_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_survey_questions');
    }
};
