<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add slug column to materials
        Schema::table('materials', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Add slug column to assignments
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Populate slugs for existing records
        $materials = DB::table('materials')->get();
        foreach ($materials as $material) {
            $baseSlug = Str::slug($material->title);
            $slug = $baseSlug ?: 'material';
            $count = 1;
            while (DB::table('materials')->where('slug', $slug)->where('id', '!=', $material->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            DB::table('materials')->where('id', $material->id)->update(['slug' => $slug]);
        }

        $assignments = DB::table('assignments')->get();
        foreach ($assignments as $assignment) {
            $baseSlug = Str::slug($assignment->title);
            $slug = $baseSlug ?: 'assignment';
            $count = 1;
            while (DB::table('assignments')->where('slug', $slug)->where('id', '!=', $assignment->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            DB::table('assignments')->where('id', $assignment->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
