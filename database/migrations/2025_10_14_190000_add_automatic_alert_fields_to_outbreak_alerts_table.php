<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outbreak_alerts', function (Blueprint $table) {
            // Add columns for automatic alert system
            $table->boolean('is_automatic')->default(false)->after('status');
            $table->integer('case_count')->default(0)->after('is_automatic');
            $table->integer('threshold_reached')->nullable()->after('case_count');
            $table->boolean('is_active')->default(true)->after('threshold_reached');
            $table->timestamp('resolved_at')->nullable()->after('is_active');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->after('resolved_at');

            // Add additional fields for existing alerts
            $table->text('affected_areas')->nullable()->after('affected_barangays');
            $table->integer('number_of_cases')->default(0)->after('affected_areas');
            $table->text('preventive_measures')->nullable()->after('health_advisory');
            $table->text('dos_and_donts')->nullable()->after('preventive_measures');
            $table->text('emergency_contacts')->nullable()->after('dos_and_donts');
        });
    }

    public function down(): void
    {
        Schema::table('outbreak_alerts', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn([
                'is_automatic',
                'case_count',
                'threshold_reached',
                'is_active',
                'resolved_at',
                'resolved_by',
                'affected_areas',
                'number_of_cases',
                'preventive_measures',
                'dos_and_donts',
                'emergency_contacts'
            ]);
        });
    }
};
