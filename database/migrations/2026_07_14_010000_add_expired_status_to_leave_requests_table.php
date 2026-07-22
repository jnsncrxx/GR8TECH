<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original `status` enum on leave_requests only listed
     * pending/approved/rejected/cancelled. LeaveRequest::EXPIRED = 'expired'
     * (used by HasExpiryWindow's expiry sweep) was never added to the
     * column definition, so writing 'expired' truncates/fails. Schema
     * builder can't alter an existing enum's values without doctrine/dbal,
     * so this uses a raw MODIFY statement instead.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE leave_requests MODIFY status ENUM('pending', 'approved', 'rejected', 'cancelled', 'expired') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * Note: rolling back while any row has status = 'expired' will fail,
     * since 'expired' would no longer be a valid enum value. Update those
     * rows first if you ever need to roll this back.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE leave_requests MODIFY status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
