<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('case_reports', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique(['case_id']);
        });

        // Add a partial unique index that only applies to non-deleted records
        // This allows the same case_id to exist if one is soft-deleted
        DB::statement('CREATE UNIQUE INDEX case_reports_case_id_unique_not_deleted ON case_reports (case_id) WHERE deleted_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the partial unique index
        DB::statement('DROP INDEX IF EXISTS case_reports_case_id_unique_not_deleted');

        Schema::table('case_reports', function (Blueprint $table) {
            // Restore the original unique constraint
            $table->unique('case_id');
        });
    }
};
