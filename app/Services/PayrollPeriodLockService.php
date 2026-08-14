<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PayrollPeriodLockService
{
    /**
     * Determine whether a date is covered by a locked payroll period that
     * applies to the employee's company, department, and employee scope.
     */
    public function isLockedForDate(string $employeeId, $date): bool
    {
        $date = Carbon::parse($date)->toDateString();

        return $this->applicableLockedPeriods($employeeId)
            ->contains(function (Period $period) use ($date) {
                return Carbon::parse($period->start_date)->toDateString() <= $date
                    && Carbon::parse($period->end_date)->toDateString() >= $date;
            });
    }

    /**
     * Determine whether any part of a date range intersects a locked payroll
     * period that applies to the employee.
     */
    public function isLockedForRange(string $employeeId, $startDate, $endDate): bool
    {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        if ($end < $start) {
            [$start, $end] = [$end, $start];
        }

        return $this->applicableLockedPeriods($employeeId)
            ->contains(function (Period $period) use ($start, $end) {
                $periodStart = Carbon::parse($period->start_date)->toDateString();
                $periodEnd = Carbon::parse($period->end_date)->toDateString();

                return $periodStart <= $end && $periodEnd >= $start;
            });
    }

    private function applicableLockedPeriods(string $employeeId): Collection
    {
        $employee = Employee::query()
            ->select(['id', 'company_id', 'department_id'])
            ->find($employeeId);

        if (! $employee) {
            return collect();
        }

        return Period::query()
            ->where('status', Period::STATUS_LOCKED)
            ->where(function ($query) use ($employee) {
                $query->where('company_id', $employee->company_id)
                    ->orWhereNull('company_id');
            })
            ->get()
            ->filter(function (Period $period) use ($employee) {
                if ($period->department_id && (string) $period->department_id !== (string) $employee->department_id) {
                    return false;
                }

                $employeeIds = $this->normalizeEmployeeIds($period->employee_ids ?? []);

                return empty($employeeIds)
                    || in_array((string) $employee->id, $employeeIds, true);
            })
            ->values();
    }

    private function normalizeEmployeeIds($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE
                ? $decoded
                : array_filter(array_map('trim', explode(',', $value)));
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_map('strval', array_filter($value))));
    }
}
