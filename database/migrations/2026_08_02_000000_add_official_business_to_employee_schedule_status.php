<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE employee_schedules MODIFY COLUMN status ENUM(
            'Working', 'Day Off', 'Leave', 'Official Business', 'Holiday', 'Overtime',
            'Regular Holiday', 'Special Holiday', 'Absent'
        ) NOT NULL DEFAULT 'Working'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::table('employee_schedules')
            ->where('status', 'Official Business')
            ->update(['status' => 'Working']);

        DB::statement("ALTER TABLE employee_schedules MODIFY COLUMN status ENUM(
            'Working', 'Day Off', 'Leave', 'Holiday', 'Overtime',
            'Regular Holiday', 'Special Holiday', 'Absent'
        ) NOT NULL DEFAULT 'Working'");
    }
};
