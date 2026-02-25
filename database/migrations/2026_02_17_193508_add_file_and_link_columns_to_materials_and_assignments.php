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
        // Add file_url and link_url to materials table
        Schema::table('materials', function (Blueprint $table) {
            $table->string('file_url')->nullable()->after('content_url');
            $table->string('link_url')->nullable()->after('file_url');
        });

        // Add file_url to assignments table (for teacher attachments)
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('file_url')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['file_url', 'link_url']);
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('file_url');
        });
    }
};
