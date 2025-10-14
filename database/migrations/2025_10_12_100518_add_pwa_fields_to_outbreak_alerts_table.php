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
        Schema::table('outbreak_alerts', function (Blueprint $table) {
            $table->text('preventive_measures')->nullable()->after('health_advisory');
            $table->text('dos_and_donts')->nullable()->after('preventive_measures');
            $table->text('emergency_contacts')->nullable()->after('dos_and_donts');
            $table->string('affected_areas')->nullable()->after('affected_barangays');
            $table->integer('number_of_cases')->default(0)->after('affected_areas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outbreak_alerts', function (Blueprint $table) {
            $table->dropColumn([
                'preventive_measures',
                'dos_and_donts',
                'emergency_contacts',
                'affected_areas',
                'number_of_cases'
            ]);
        });
    }
};
