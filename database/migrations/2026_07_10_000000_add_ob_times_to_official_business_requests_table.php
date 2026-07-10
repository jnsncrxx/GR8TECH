<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('official_business_requests', function (Blueprint $table) {
            // Full-day OB (default): credited_hours is filled in from the employee's
            // scheduled shift length for that date at approval time.
            // Partial-day OB: ob_start_time/ob_end_time are required, and
            // credited_hours is computed from their difference instead.
            $table->boolean('is_full_day')->default(true)->after('reason');
            $table->time('ob_start_time')->nullable()->after('is_full_day');
            $table->time('ob_end_time')->nullable()->after('ob_start_time');

            // Snapshot of the actual hours credited for this request, computed once
            // at approval time (from schedule or from ob_start_time/ob_end_time) and
            // copied onto the resulting AttendanceRecord. Stored here too so the OB
            // table can display a Duration column without recomputing on every load.
            $table->decimal('credited_hours', 5, 2)->nullable()->after('ob_end_time');
        });
    }

    public function down(): void
    {
        Schema::table('official_business_requests', function (Blueprint $table) {
            $table->dropColumn(['is_full_day', 'ob_start_time', 'ob_end_time', 'credited_hours']);
        });
    }
};
