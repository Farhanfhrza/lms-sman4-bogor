<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Populate existing records with UUIDs
        DB::table('quiz_attempts')->whereNull('uuid')->cursor()->each(function ($attempt) {
            DB::table('quiz_attempts')
                ->where('id', $attempt->id)
                ->update(['uuid' => Str::uuid()->toString()]);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
