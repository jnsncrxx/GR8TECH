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
        Schema::table('leave_requests', function (Blueprint $table) {
            // Tracks whether this leave request is paid or unpaid.
            // - false (default): no leave balance has been set for this employee,
            //   so every request is counted as unpaid/increment only.
            // - true: a leave balance was in place when the request was filed AND
            //   the leave type is one of the paid types (vacation, sick, sil).
            $table->boolean('is_paid')->default(false)->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
};
