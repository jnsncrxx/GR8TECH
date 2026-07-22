<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->uuid('corrected_by')->nullable()->after('notes');
            $table->text('correction_reason')->nullable()->after('corrected_by');
            $table->timestamp('corrected_at')->nullable()->after('correction_reason');
        });

        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('attendance_record_id')->index();
            $table->uuid('corrected_by')->nullable()->index();
            $table->text('reason');
            $table->json('original_values');
            $table->json('corrected_values');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['corrected_by', 'correction_reason', 'corrected_at']);
        });
    }
};
