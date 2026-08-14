<?php

namespace App\Services;

use App\Helpers\CompanyHelper;
use App\Models\Company;
use Carbon\Carbon;

/**
 * Resolves payroll cutoff period boundaries and the OB approval grace deadline
 * for a given date. Cutoff days come from the active Company record
 * (Company::cutoff_day_1 / cutoff_day_2, default 10th/25th) rather than
 * config/attendance_cutoff.php, so each company can run its own payroll
 * cycle. Grace hours are still read from config('attendance_cutoff.grace_period_hours')
 * — that wasn't part of the per-company migration.
 *
 * IMPORTANT: If the app's existing Period Management module (see
 * app/Http/Controllers/Web/PeriodManagementController.php) already stores cutoff
 * date ranges in a `periods` table, this class should be repointed to read from
 * that model instead, so OB approvals agree with actual payroll period
 * boundaries. Everything that consumes this service — OB filing, approval, and
 * the expiry sweep — stays unchanged either way, since they only ever call
 * periodFor() / graceDeadlineFor() / isOpenForAction().
 */
class CutoffPeriodService
{
    /**
     * The cutoff period a given date falls into.
     *
     * @param  Company|null  $company  Defaults to the session's active company
     *                                  (CompanyHelper::getCurrentCompany()). Falls
     *                                  back to config('attendance_cutoff.cutoff_days')
     *                                  if no company is resolvable, so console
     *                                  commands and jobs running outside a
     *                                  session don't break.
     * @return array{key: string, start: Carbon, end: Carbon}
     */
    public function periodFor($date, ?Company $company = null): array
    {
        $date = Carbon::parse($date)->startOfDay();
        $company = $company ?? CompanyHelper::getCurrentCompany();

        $cutoffDays = $company && $company->cutoff_day_1 && $company->cutoff_day_2
            ? collect($company->cutoffDays())
            : collect(config('attendance_cutoff.cutoff_days', [10, 25]));
        $cutoffDays = $cutoffDays->sort()->values();

        // Build cutoff end-instants spanning one month before/after $date so
        // month-boundary wraparound (e.g. Jan 28 -> Feb 10 period) resolves correctly.
        $cutoffInstants = collect([-1, 0, 1])
            ->flatMap(function (int $monthOffset) use ($date, $cutoffDays) {
                $month = $date->copy()->addMonthsNoOverflow($monthOffset);
                $lastDayOfMonth = $month->copy()->endOfMonth()->day;

                return $cutoffDays->map(function (int $day) use ($month, $lastDayOfMonth) {
                    // Clamp so a configured cutoff day like 30 doesn't overflow Feb.
                    $safeDay = min($day, $lastDayOfMonth);
                    return $month->copy()->day($safeDay)->endOfDay();
                });
            })
            ->sortBy(fn (Carbon $c) => $c->timestamp)
            ->values();

        $end = $cutoffInstants->first(fn (Carbon $c) => $c->greaterThanOrEqualTo($date));
        $endIndex = $cutoffInstants->search($end);
        $previous = $endIndex > 0
            ? $cutoffInstants[$endIndex - 1]
            : $end->copy()->subMonthNoOverflow();

        $start = $previous->copy()->addDay()->startOfDay();

        return [
            'key' => $start->format('Y-m-d') . '_' . $end->format('Y-m-d'),
            'start' => $start,
            'end' => $end,
        ];
    }

    public function currentPeriod(?Company $company = null): array
    {
        return $this->periodFor(Carbon::now(), $company);
    }

    /**
     * Hard deadline by which a pending OB request tied to $date's cutoff period
     * must be approved before it auto-expires (period end + grace hours).
     */
    public function graceDeadlineFor($date, ?Company $company = null): Carbon
    {
        $period = $this->periodFor($date, $company);
        $graceHours = (int) config('attendance_cutoff.grace_period_hours', 24);

        return $period['end']->copy()->addHours($graceHours);
    }

    /**
     * Whether $date's cutoff period is still open for filing/approval action,
     * i.e. we haven't passed its grace deadline yet.
     */
    public function isOpenForAction($date, ?Company $company = null): bool
    {
        return Carbon::now()->lessThanOrEqualTo($this->graceDeadlineFor($date, $company));
    }
}
