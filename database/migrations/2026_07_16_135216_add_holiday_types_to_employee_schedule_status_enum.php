<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support ENUM/MODIFY COLUMN - it stores status as
        // a plain string column already, so any value is accepted there.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE employee_schedules MODIFY COLUMN status ENUM(
            'Working', 'Day Off', 'Leave', 'Holiday', 'Overtime', 'Regular Holiday', 'Special Holiday', 'Absent'
        ) NOT NULL DEFAULT 'Working'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE employee_schedules MODIFY COLUMN status ENUM(
            'Working', 'Day Off', 'Leave', 'Holiday', 'Overtime'
        ) NOT NULL DEFAULT 'Working'");
    }
};
