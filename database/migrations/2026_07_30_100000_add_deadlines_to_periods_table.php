<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->timestamp('request_deadline_at')->nullable()->after('end_date');
            $table->timestamp('preparation_deadline_at')->nullable()->after('request_deadline_at');
            $table->timestamp('validation_deadline_at')->nullable()->after('preparation_deadline_at');
            $table->timestamp('lock_deadline_at')->nullable()->after('validation_deadline_at');
            $table->timestamp('deadline_extended_at')->nullable()->after('lock_deadline_at');
            $table->uuid('deadline_extended_by')->nullable()->after('deadline_extended_at');
            $table->text('deadline_extension_reason')->nullable()->after('deadline_extended_by');
        });

        DB::table('periods')->orderBy('created_at')->get()->each(function ($period) {
            $end = Carbon::parse($period->end_date)->endOfDay();
            $payrollDate = $period->payroll_date
                ? Carbon::parse($period->payroll_date)
                : $end->copy()->addDays(5);

            DB::table('periods')->where('id', $period->id)->update([
                'request_deadline_at' => $end->copy()->addHours(24),
                'preparation_deadline_at' => $end->copy()->addDays(2),
                'validation_deadline_at' => $end->copy()->addDays(3),
                'lock_deadline_at' => $payrollDate->copy()->subDay()->endOfDay(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'request_deadline_at',
                'preparation_deadline_at',
                'validation_deadline_at',
                'lock_deadline_at',
                'deadline_extended_at',
                'deadline_extended_by',
                'deadline_extension_reason',
            ]);
        });
    }
};
