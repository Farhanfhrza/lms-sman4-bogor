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
        // Make created_by nullable in materials table
        Schema::table('materials', function (Blueprint $table) {
            $table->bigInteger('created_by')->nullable()->change();
        });

        // Make created_by nullable in assignments table
        Schema::table('assignments', function (Blueprint $table) {
            $table->bigInteger('created_by')->nullable()->change();
        });

        // Make created_by nullable in quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            $table->bigInteger('created_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to NOT NULL
        Schema::table('materials', function (Blueprint $table) {
            $table->bigInteger('created_by')->nullable(false)->change();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->bigInteger('created_by')->nullable(false)->change();
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->bigInteger('created_by')->nullable(false)->change();
        });
    }
};
