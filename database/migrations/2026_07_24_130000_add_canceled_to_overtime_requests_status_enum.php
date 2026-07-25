<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * overtime_requests.status was enum('pending','approved','rejected','expired')
     * with no 'canceled' value, which is what caused:
     *   SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status'
     * when OvertimeController::cancel() tried to save OvertimeRequest::CANCELED.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE overtime_requests MODIFY COLUMN status ENUM('pending','approved','rejected','expired','canceled') NOT NULL DEFAULT 'pending'"
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: this will fail if any row already has status = 'canceled' at
     * rollback time, since MySQL can't narrow an enum while a row still
     * references the value being removed. Change any such rows first if
     * you ever need to roll this back.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE overtime_requests MODIFY COLUMN status ENUM('pending','approved','rejected','expired') NOT NULL DEFAULT 'pending'"
            );
        }
    }
};
