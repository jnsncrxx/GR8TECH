<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OfficialBusinessRequest;
use App\Models\OvertimeRequest;
use App\Models\Period;

class PayrollRequestConflictService
{
    /**
     * Whether payroll has already been generated for this employee's
     * company covering the given single date (OB / Overtime requests).
     * Cancelling or editing an approved request past this point would
     * change attendance/leave/OT data underneath a payroll that's already
     * been processed, reviewed, finalized, or locked.
     */
    public function payrollGeneratedForDate(string $employeeId, string $date): bool
    {
        $employee = Employee::find($employeeId);

        if (!$employee || !$employee->company_id) {
            return false;
        }

        return Period::hasGeneratedPayrollForDate($employee->company_id, $date);
    }

    /**
     * Range variant for Leave requests, which span start_date to end_date
     * rather than a single date.
     */
    public function payrollGeneratedForRange(string $employeeId, string $start, string $end): bool
    {
        $employee = Employee::find($employeeId);

        if (!$employee || !$employee->company_id) {
            return false;
        }

        return Period::hasGeneratedPayrollOverlapping($employee->company_id, $start, $end);
    }

    public function leaveOnDate(string $employeeId, string $date, ?string $exceptId = null): bool
    {
        return LeaveRequest::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', [LeaveRequest::PENDING, LeaveRequest::APPROVED])
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }

    public function overtimeOnDate(string $employeeId, string $date, ?string $exceptId = null): bool
    {
        return OvertimeRequest::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', [OvertimeRequest::PENDING, OvertimeRequest::APPROVED])
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->whereDate('date', $date)
            ->exists();
    }

    public function officialBusinessOnDate(string $employeeId, string $date, ?string $exceptId = null): bool
    {
        return OfficialBusinessRequest::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', [OfficialBusinessRequest::PENDING, OfficialBusinessRequest::APPROVED])
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->whereDate('date', $date)
            ->exists();
    }

    public function timeRequestWithinLeaveRange(string $employeeId, string $start, string $end): ?string
    {
        if (OvertimeRequest::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', [OvertimeRequest::PENDING, OvertimeRequest::APPROVED])
            ->whereBetween('date', [$start, $end])
            ->exists()) {
            return 'overtime';
        }

        if (OfficialBusinessRequest::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', [OfficialBusinessRequest::PENDING, OfficialBusinessRequest::APPROVED])
            ->whereBetween('date', [$start, $end])
            ->exists()) {
            return 'Official Business';
        }

        return null;
    }
}