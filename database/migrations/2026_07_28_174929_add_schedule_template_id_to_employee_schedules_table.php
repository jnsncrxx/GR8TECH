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
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->uuid('schedule_template_id')->nullable()->after('required_hours');
            $table->foreign('schedule_template_id')->references('id')->on('schedule_templates')->onDelete('set null');
            $table->index('schedule_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_schedules', function (Blueprint $table) {
            $table->dropForeign(['schedule_template_id']);
            $table->dropIndex(['schedule_template_id']);
            $table->dropColumn('schedule_template_id');
        });
    }
};