<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoGrantSilBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:auto-grant-sil
                            {--dry-run : Show which employees would be granted SIL without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically activate deferred SIL leave balance for employees who have completed 1 year as a regular employee.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('[DRY RUN] No changes will be saved.');
        }

        // Find all deferred SIL balance records.
        $deferredBalances = LeaveBalance::where('sil_deferred', true)
            ->where('is_balance_set', true)
            ->with('employee')
            ->get();

        if ($deferredBalances->isEmpty()) {
            $this->info('No deferred SIL balances found. Nothing to do.');
            return self::SUCCESS;
        }

        $grantedCount = 0;
        $skippedCount = 0;

        foreach ($deferredBalances as $balance) {
            $employee = $balance->employee;

            if (!$employee) {
                $this->warn("Balance {$balance->id}: employee record not found — skipping.");
                $skippedCount++;
                continue;
            }

            if (!$employee->hasCompletedOneYearAsRegular()) {
                $anchor = $employee->regularAnchorDate();
                $grantDate = $anchor ? $anchor->addYear()->toDateString() : 'unknown';
                $this->line("  Skipped  [{$employee->employee_id}] {$employee->full_name} — 1-year anniversary: {$grantDate}");
                $skippedCount++;
                continue;
            }

            // Employee has completed 1 year — activate the SIL balance.
            $this->line("  Granting [{$employee->employee_id}] {$employee->full_name} — SIL {$balance->sil_days_total} days");

            if (!$isDryRun) {
                $balance->sil_deferred = false;
                $balance->save();

                Log::info('SIL balance auto-granted after 1-year anniversary', [
                    'employee_id'   => $employee->id,
                    'employee_code' => $employee->employee_id,
                    'balance_id'    => $balance->id,
                    'sil_days'      => $balance->sil_days_total,
                    'anchor_date'   => $employee->regularAnchorDate()?->toDateString(),
                ]);
            }

            $grantedCount++;
        }

        $this->newLine();
        $this->info("Done. Granted: {$grantedCount} | Skipped: {$skippedCount}");

        return self::SUCCESS;
    }
}
