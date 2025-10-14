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
        Schema::table('case_reports', function (Blueprint $table) {
            $table->text('validator_feedback')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_reports', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['validator_feedback', 'returned_by', 'returned_at']);
        });
    }
};
