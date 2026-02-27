<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('course_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('class_subjects')
                  ->nullOnDelete();

            $table->string('description', 255)->nullable()->after('action');
            $table->string('ip_address', 45)->nullable()->after('target_id');

            // Indexes for fast filtering
            $table->index('course_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['action']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['course_id', 'description', 'ip_address']);
        });
    }
};
