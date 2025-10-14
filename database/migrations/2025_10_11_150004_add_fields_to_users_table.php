<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('facility_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->foreignId('municipality_id')->nullable()->after('facility_id')->constrained()->onDelete('set null');
            $table->string('position')->nullable()->after('name');
            $table->string('contact_number')->nullable()->after('email');
            $table->enum('user_type', ['encoder', 'validator', 'pesu_admin'])->default('encoder')->after('contact_number');
            $table->boolean('is_active')->default(true)->after('user_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
            $table->dropForeign(['municipality_id']);
            $table->dropColumn(['facility_id', 'municipality_id', 'position', 'contact_number', 'user_type', 'is_active']);
        });
    }
};
