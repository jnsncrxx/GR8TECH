<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\OfficialBusinessRequest;
use App\Models\OvertimeRequest;

class PayrollRequestConflictService
{
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
