<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbreak_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->string('alert_title');
            $table->text('alert_details');
            $table->enum('alert_level', ['Green', 'Yellow', 'Orange', 'Red']);
            $table->foreignId('municipality_id')->nullable()->constrained()->onDelete('set null');
            $table->json('affected_barangays')->nullable();
            $table->date('alert_start_date');
            $table->date('alert_end_date')->nullable();
            $table->text('health_advisory')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbreak_alerts');
    }
};
