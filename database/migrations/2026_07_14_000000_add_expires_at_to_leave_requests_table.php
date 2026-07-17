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
            // Grace deadline set at filing time (see
            // LeaveController::graceDeadlineFor()) and read by the
            // HasExpiryWindow trait on LeaveRequest to know when a pending
            // request should flip to 'expired'.
            $table->timestamp('expires_at')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
