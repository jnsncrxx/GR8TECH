<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->uuid('company_id')->nullable()->after('id');
            $table->uuid('previous_period_id')->nullable()->after('company_id');
            $table->unsignedTinyInteger('period_month')->nullable()->after('description');
            $table->unsignedSmallInteger('period_year')->nullable()->after('period_month');
            $table->unsignedTinyInteger('period_no')->nullable()->after('period_year');
            $table->string('period_type', 30)->default('regular')->after('period_no');
            $table->string('processing_type', 30)->default('regular')->after('period_type');
            $table->date('payroll_date')->nullable()->after('processing_type');
            $table->unsignedSmallInteger('working_days')->default(0)->after('end_date');
            $table->string('status', 30)->default('draft')->after('working_days');
            $table->timestamp('locked_at')->nullable()->after('created_by');
            $table->string('locked_by')->nullable()->after('locked_at');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();

            $table->foreign('previous_period_id')
                ->references('id')
                ->on('periods')
                ->nullOnDelete();

            $table->index(['company_id', 'start_date', 'end_date'], 'periods_company_dates_index');
            $table->index(['company_id', 'period_year', 'period_month'], 'periods_company_cycle_index');
        });

        // Backfill existing records so old periods remain readable.
        DB::table('periods')
            ->orderBy('created_at')
            ->get()
            ->each(function ($period) {
                $start = \Carbon\Carbon::parse($period->start_date);
                $end = \Carbon\Carbon::parse($period->end_date);
                $workingDays = 0;
                $cursor = $start->copy();

                while ($cursor->lte($end)) {
                    if ($cursor->isWeekday()) {
                        $workingDays++;
                    }

                    $cursor->addDay();
                }

                DB::table('periods')
                    ->where('id', $period->id)
                    ->update([
                        'period_month' => $end->month,
                        'period_year' => $end->year,
                        'period_no' => 1,
                        'period_type' => 'regular',
                        'processing_type' => 'regular',
                        'payroll_date' => $end->copy()->addDays(5)->toDateString(),
                        'working_days' => $workingDays,
                        'status' => 'draft',
                    ]);
            });

        // Prevent duplicate numbered periods within a company.
        Schema::table('periods', function (Blueprint $table) {
            $table->unique(
                ['company_id', 'period_year', 'period_month', 'period_no'],
                'periods_company_cycle_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropUnique('periods_company_cycle_unique');
            $table->dropIndex('periods_company_dates_index');
            $table->dropIndex('periods_company_cycle_index');
            $table->dropForeign(['previous_period_id']);
            $table->dropForeign(['company_id']);

            $table->dropColumn([
                'company_id',
                'previous_period_id',
                'period_month',
                'period_year',
                'period_no',
                'period_type',
                'processing_type',
                'payroll_date',
                'working_days',
                'status',
                'locked_at',
                'locked_by',
            ]);
        });
    }
};
