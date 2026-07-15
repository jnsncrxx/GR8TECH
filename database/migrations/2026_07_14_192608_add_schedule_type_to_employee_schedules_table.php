<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->enum('schedule_type', ['fixed', 'flexible'])
                ->default('fixed')
                ->after('status');

            $table->decimal('required_hours', 4, 2)
                ->default(8.00)
                ->after('schedule_type');
        });
    }

    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropColumn(['schedule_type', 'required_hours']);
        });
    }
};