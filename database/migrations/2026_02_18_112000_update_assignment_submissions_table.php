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
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->string('submission_url')->nullable()->change();
            $table->string('file_url')->nullable()->after('submission_url');
            $table->string('link_url')->nullable()->after('file_url');
            $table->text('submission_text')->nullable()->after('link_url');
            $table->string('status')->default('submitted')->after('submission_text'); // Add status if missing, or maybe checks first?
            // Actually model usage suggests 'status' column is used (line 131 in controller).
            // Let me check if 'status' exists in create_assignment_submissions?
            // Reading Step 370: No 'status' column in create_assignment_submissions.
            // So I should add that too.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->string('submission_url')->nullable(false)->change();
            $table->dropColumn(['file_url', 'link_url', 'submission_text', 'status']);
        });
    }
};
