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
        // SQLite doesn't support MODIFY COLUMN, so we'll recreate the table
        Schema::create('diseases_new', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['Contagious', 'Non-Contagious']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Copy data with updated categories
        DB::statement("
            INSERT INTO diseases_new (id, name, category, description, is_active, created_at, updated_at)
            SELECT
                id,
                name,
                CASE
                    WHEN category = 'Category I' THEN 'Contagious'
                    WHEN category = 'Category II' THEN 'Non-Contagious'
                    ELSE category
                END as category,
                description,
                is_active,
                created_at,
                updated_at
            FROM diseases
        ");

        // Drop old table and rename new one
        Schema::dropIfExists('diseases');
        Schema::rename('diseases_new', 'diseases');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate with original categories
        Schema::create('diseases_old', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['Category I', 'Category II']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Copy data back with original categories
        DB::statement("
            INSERT INTO diseases_old (id, name, category, description, is_active, created_at, updated_at)
            SELECT
                id,
                name,
                CASE
                    WHEN category = 'Contagious' THEN 'Category I'
                    WHEN category = 'Non-Contagious' THEN 'Category II'
                    ELSE category
                END as category,
                description,
                is_active,
                created_at,
                updated_at
            FROM diseases
        ");

        // Drop new table and rename old one back
        Schema::dropIfExists('diseases');
        Schema::rename('diseases_old', 'diseases');
    }
};
