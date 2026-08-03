<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;

class AttendanceExceptionService
{
    /**
     * Return every exception for one employee/day in priority order.
     */
    public function evaluate(
        ?AttendanceRecord $record,
        ?EmployeeSchedule $schedule,
        float $workedHours,
        bool $hasApprovedLeave = false,
        bool $hasApprovedOfficialBusiness = false,
        float $approvedOvertimeHours = 0.0
    ): array {
        $issues = [];

        if (!$schedule) {
            $issues[] = $this->issue('missing_schedule', 'Missing schedule', 'blocking');
        }

        if ($record?->status === AttendanceRecord::ON_LEAVE && !$hasApprovedLeave) {
            $issues[] = $this->issue('unverified_leave', 'No approved leave request', 'blocking');
        }

        if ($record?->status === AttendanceRecord::OFFICIAL_BUSINESS && !$hasApprovedOfficialBusiness) {
            $issues[] = $this->issue('unverified_official_business', 'No approved OB request', 'blocking');
        }

        if ($record && (($record->time_in && !$record->time_out) || (!$record->time_in && $record->time_out))) {
            $issues[] = $this->issue('incomplete', 'Incomplete log', 'blocking');
        }

        if ($record && ($record->hasInvalidTimeSpan() || $workedHours > 24)) {
            $issues[] = $this->issue('invalid_duration', 'Invalid duration', 'blocking');
        }

        if ($record?->status === AttendanceRecord::ABSENT && ($record->time_in || $record->time_out)) {
            $issues[] = $this->issue('absence_with_attendance', 'Absent status has attendance punches', 'blocking');
        }

        if ($hasApprovedLeave && ($workedHours > 0 || $hasApprovedOfficialBusiness || $approvedOvertimeHours > 0)) {
            $issues[] = $this->issue('leave_conflict', 'Approved leave conflicts with attendance or another request', 'blocking');
        }

        if ($approvedOvertimeHours > 0 && (!$record?->time_in || !$record?->time_out)) {
            $issues[] = $this->issue('ot_without_attendance', 'OT without complete attendance', 'blocking');
        }

        $scheduledHours = (float) ($schedule?->required_hours ?: $schedule?->working_hours ?: 0);
        if (
            $approvedOvertimeHours > 0
            && $schedule
            && !in_array($schedule->status, ['Day Off', 'Rest Day'], true)
            && $scheduledHours > 0
            && $workedHours < $scheduledHours
        ) {
            $issues[] = $this->issue('ot_before_required_hours', 'OT before required hours', 'blocking');
        }

        if ($hasApprovedLeave && $approvedOvertimeHours > 0) {
            $issues[] = $this->issue('ot_overlaps_leave', 'OT overlaps approved leave', 'blocking');
        }

        if (
            $schedule
            && in_array($schedule->status, ['Day Off', 'Rest Day'], true)
            && $workedHours > 0
            && !$record?->corrected_at
        ) {
            $issues[] = $this->issue('rest_day_attendance', 'Rest-day duty review', 'review');
        }

        if (
            $record?->time_in
            && $schedule?->status === 'Working'
            && $schedule->time_in
            && !$record->corrected_at
        ) {
            $date = Carbon::parse($record->date)->format('Y-m-d');
            $scheduledIn = Carbon::parse($date.' '.Carbon::parse($schedule->time_in)->format('H:i:s'));
            $actualIn = Carbon::parse($record->time_in);

            if (abs($scheduledIn->diffInMinutes($actualIn, false)) >= 4 * 60) {
                $issues[] = $this->issue('possible_wrong_schedule', 'Possible wrong schedule', 'review');
            }
        }

        return collect($issues)->unique('code')->values()->all();
    }

    public function primary(
        array $issues,
        bool $hasApprovedLeave = false,
        bool $hasApprovedOb = false,
        ?AttendanceRecord $record = null,
        ?EmployeeSchedule $schedule = null,
        float $workedHours = 0.0
    ): array
    {
        if ($issues !== []) {
            return $issues[0];
        }

        if ($hasApprovedLeave) {
            return $this->issue('clear', 'Approved leave', 'clear');
        }

        if ($hasApprovedOb) {
            return $this->issue('clear', 'Approved official business', 'clear');
        }

        if ($record?->status === AttendanceRecord::ABSENT && !$record->time_in && !$record->time_out) {
            return $this->issue('clear', 'Recorded absence', 'clear');
        }

        if ($record?->corrected_at) {
            if (
                $schedule
                && in_array($schedule->status, ['Day Off', 'Rest Day'], true)
                && $workedHours > 0
            ) {
                return $this->issue('clear', 'Rest-day duty reviewed', 'clear');
            }

            return $this->issue('clear', 'Reviewed correction', 'clear');
        }

        return $this->issue('clear', 'No exception', 'clear');
    }

    private function issue(string $code, string $label, string $severity): array
    {
        return compact('code', 'label', 'severity');
    }
}
