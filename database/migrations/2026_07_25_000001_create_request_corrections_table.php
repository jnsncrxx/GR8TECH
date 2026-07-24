<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Generic audit trail for edits made to already-approved Leave, Official
     * Business, and Overtime requests. Mirrors the attendance_corrections
     * pattern: one row per edit, with a required reason and a before/after
     * snapshot so an approved-then-edited request is always traceable.
     */
    public function up(): void
    {
        Schema::create('request_corrections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('request_type', 20); // 'leave' | 'official_business' | 'overtime'
            $table->uuid('request_id')->index();
            $table->uuid('corrected_by')->nullable()->index();
            $table->text('reason');
            $table->json('original_values');
            $table->json('corrected_values');
            $table->timestamps();

            $table->index(['request_type', 'request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_corrections');
    }
};
