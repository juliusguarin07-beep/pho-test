<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_report_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->enum('attachment_type', ['Lab Result', 'Signed CIF', 'Referral', 'Death Certificate', 'Other']);
            $table->text('remarks')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_attachments');
    }
};
