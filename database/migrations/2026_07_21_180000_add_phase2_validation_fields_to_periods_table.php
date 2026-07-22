<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->timestamp('attendance_validated_at')->nullable()->after('status');
            $table->uuid('attendance_validated_by')->nullable()->after('attendance_validated_at');

            $table->timestamp('leave_validated_at')->nullable()->after('attendance_validated_by');
            $table->uuid('leave_validated_by')->nullable()->after('leave_validated_at');

            $table->timestamp('ob_validated_at')->nullable()->after('leave_validated_by');
            $table->uuid('ob_validated_by')->nullable()->after('ob_validated_at');

            $table->timestamp('overtime_validated_at')->nullable()->after('ob_validated_by');
            $table->uuid('overtime_validated_by')->nullable()->after('overtime_validated_at');

            $table->text('validation_notes')->nullable()->after('overtime_validated_by');

            $table->timestamp('ready_at')->nullable()->after('validation_notes');
            $table->uuid('ready_by')->nullable()->after('ready_at');
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_validated_at',
                'attendance_validated_by',
                'leave_validated_at',
                'leave_validated_by',
                'ob_validated_at',
                'ob_validated_by',
                'overtime_validated_at',
                'overtime_validated_by',
                'validation_notes',
                'ready_at',
                'ready_by',
            ]);
        });
    }
};
