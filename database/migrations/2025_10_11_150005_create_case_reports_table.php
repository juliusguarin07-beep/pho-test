<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_reports', function (Blueprint $table) {
            $table->id();
            $table->string('case_id')->unique();

            // Section A: Case Identification
            $table->foreignId('disease_id')->constrained()->onDelete('cascade');
            $table->string('disease_other')->nullable();
            $table->date('date_reported');
            $table->enum('case_classification', ['Suspect', 'Probable', 'Confirmed']);
            $table->enum('outcome', ['Alive', 'Died', 'Ongoing Treatment']);

            // Section B: Patient Information
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Other']);
            $table->date('date_of_birth');
            $table->integer('age')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address');
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            $table->foreignId('municipality_id')->constrained()->onDelete('cascade');
            $table->string('contact_number')->nullable();
            $table->enum('pregnancy_status', ['Pregnant', 'Not Pregnant', 'N/A'])->default('N/A');
            $table->string('philhealth_number')->nullable();
            $table->string('nationality')->default('Filipino');

            // Section C: Clinical Information
            $table->date('date_of_onset');
            $table->date('date_of_consultation')->nullable();
            $table->foreignId('admitting_facility_id')->nullable()->constrained('facilities')->onDelete('set null');
            $table->json('signs_symptoms')->nullable();
            $table->string('signs_symptoms_other')->nullable();
            $table->integer('duration_of_illness')->nullable();
            $table->string('clinical_classification')->nullable();
            $table->text('complications')->nullable();
            $table->enum('clinical_outcome', ['Alive', 'Died', 'Unknown'])->default('Alive');
            $table->date('date_of_death')->nullable();
            $table->string('final_diagnosis')->nullable();
            $table->text('clinical_remarks')->nullable();

            // Section D: Laboratory Information
            $table->date('specimen_collection_date')->nullable();
            $table->string('specimen_type')->nullable();
            $table->string('laboratory_test')->nullable();
            $table->date('laboratory_result_date')->nullable();
            $table->enum('laboratory_result', ['Positive', 'Negative', 'Pending'])->nullable();
            $table->string('testing_laboratory')->nullable();
            $table->string('laboratory_file_path')->nullable();

            // Section E: Exposure & Travel History
            $table->string('place_of_exposure')->nullable();
            $table->date('date_of_exposure')->nullable();
            $table->text('travel_history')->nullable();
            $table->date('travel_departure_date')->nullable();
            $table->date('travel_return_date')->nullable();
            $table->string('mode_of_exposure')->nullable();
            $table->string('source_of_infection')->nullable();

            // Section F: Contact Tracing
            $table->boolean('contacts_identified')->default(false);
            $table->integer('number_of_contacts')->default(0);
            $table->text('contact_details')->nullable();
            $table->string('relationship_to_case')->nullable();
            $table->date('date_contacted')->nullable();
            $table->string('quarantine_status')->nullable();

            // Section G: Reporting Information
            $table->foreignId('reporting_facility_id')->constrained('facilities')->onDelete('cascade');
            $table->string('facility_code')->nullable();
            $table->string('reporting_health_worker');
            $table->string('health_worker_designation')->nullable();
            $table->string('health_worker_contact')->nullable();
            $table->date('date_reported_to_dess')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validation_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approval_date')->nullable();
            $table->text('validation_remarks')->nullable();

            // Status tracking
            $table->enum('status', ['draft', 'submitted', 'returned', 'validated', 'approved'])->default('draft');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_reports');
    }
};
