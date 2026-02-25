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
        Schema::create('teacher_survey_response_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('teacher_survey_responses')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('teacher_survey_questions')->cascadeOnDelete();
            $table->integer('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_survey_response_details');
    }
};
