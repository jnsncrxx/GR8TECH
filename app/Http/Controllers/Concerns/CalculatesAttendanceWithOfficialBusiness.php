<?php

namespace App\Http\Controllers\Concerns;

use App\Models\AttendanceRecord;
use App\Models\OfficialBusinessRequest;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;

/**
 * Centralized attendance + Official Business (OB) interval strategy.
 *
 * This trait is the single source of truth for:
 * - normalizing arbitrary date/time values,
 * - constructing safe Carbon intervals (never by string concatenation),
 * - merging overlapping/adjacent intervals into unique minutes,
 * - recalculating attendance as the unique union of actual worked time
 *   and approved OB time (overlaps counted once; OB never adds overtime).
 *
 * It exists so that the OB approval flow and the time-in/out flow share
 * ONE implementation and cannot diverge (the divergence is what produced
 * the "2026-07-14 2026-07-14 08:00:00" double-date bug).
 */
trait CalculatesAttendanceWithOfficialBusiness
{
    /**
     * Application timezone.
     */
    private function timezone(): string
    {
        return config('app.timezone', 'UTC');
    }

    /**
     * Normalize any date/datetime value to Y-m-d.
     */
    private function normalizeDate(mixed $value): string
    {
        if ($value instanceof CarbonInterface) {
            return $value->copy()->toDateString();
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        $value = trim((string) $value);

        if (
            preg_match(
                '/^\d{4}-\d{2}-\d{2}/',
                $value,
                $matches
            )
        ) {
            return $matches[0];
        }

        return Carbon::parse(
            $value,
            $this->timezone()
        )->toDateString();
    }

    /**
     * Normalize any time/datetime value to H:i:s.
     *
     * Supported:
     * - 08:00
     * - 08:00:00
     * - 2026-07-14 08:00:00
     * - 2026-07-14T08:00:00
     * - Carbon
     * - DateTime
     */
    private function normalizeTime(mixed $value): string
    {
        if ($value instanceof CarbonInterface) {
            return $value->format('H:i:s');
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->format('H:i:s');
        }

        $value = trim((string) $value);

        /*
         * Extract the time directly.
         *
         * Example:
         * 2026-07-14 08:00:00
         * becomes:
         * 08:00:00
         *
         * This prevents:
         * 2026-07-14 2026-07-14 08:00:00
         */
        if (
            preg_match(
                '/(?:^|[T\s])(\d{2}:\d{2}(?::\d{2})?)(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?$/',
                $value,
                $matches
            )
        ) {
            $time = $matches[1];

            if (strlen($time) === 5) {
                return $time . ':00';
            }

            return $time;
        }

        /*
         * Plain HH:mm.
         */
        if (
            preg_match(
                '/^\d{2}:\d{2}$/',
                $value
            )
        ) {
            return $value . ':00';
        }

        /*
         * Plain HH:mm:ss.
         */
        if (
            preg_match(
                '/^\d{2}:\d{2}:\d{2}$/',
                $value
            )
        ) {
            return $value;
        }

        return Carbon::parse(
            $value,
            $this->timezone()
        )->format('H:i:s');
    }

    /**
     * Create a normalized date/time interval.
     *
     * IMPORTANT:
     * Date and time are normalized separately before
     * constructing the final Carbon instances.
     */
    private function createInterval(
        mixed $date,
        mixed $startTime,
        mixed $endTime
    ): array {
        $dateOnly = $this->normalizeDate($date);

        $startTimeOnly = $this->normalizeTime(
            $startTime
        );

        $endTimeOnly = $this->normalizeTime(
            $endTime
        );

        $start = Carbon::create(
            (int) substr($dateOnly, 0, 4),
            (int) substr($dateOnly, 5, 2),
            (int) substr($dateOnly, 8, 2),
            (int) substr($startTimeOnly, 0, 2),
            (int) substr($startTimeOnly, 3, 2),
            (int) substr($startTimeOnly, 6, 2),
            $this->timezone()
        );

        $end = Carbon::create(
            (int) substr($dateOnly, 0, 4),
            (int) substr($dateOnly, 5, 2),
            (int) substr($dateOnly, 8, 2),
            (int) substr($endTimeOnly, 0, 2),
            (int) substr($endTimeOnly, 3, 2),
            (int) substr($endTimeOnly, 6, 2),
            $this->timezone()
        );

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Calculate unique minutes.
     *
     * Overlapping intervals are merged.
     */
    private function calculateUniqueMinutes(
        array $intervals
    ): int {
        $intervals = array_values(
            array_filter(
                $intervals,
                function (array $interval): bool {
                    return isset(
                        $interval['start'],
                        $interval['end']
                    )
                        && $interval['start']
                            instanceof CarbonInterface
                        && $interval['end']
                            instanceof CarbonInterface
                        && $interval['end']->gt(
                            $interval['start']
                        );
                }
            )
        );

        if (empty($intervals)) {
            return 0;
        }

        usort(
            $intervals,
            fn (array $a, array $b) =>
                $a['start']->timestamp
                <=>
                $b['start']->timestamp
        );

        $merged = [];

        foreach ($intervals as $interval) {
            $current = [
                'start' => $interval['start']->copy(),
                'end' => $interval['end']->copy(),
            ];

            if (empty($merged)) {
                $merged[] = $current;

                continue;
            }

            $lastIndex = count($merged) - 1;

            if (
                $current['start']->lte(
                    $merged[$lastIndex]['end']
                )
            ) {
                if (
                    $current['end']->gt(
                        $merged[$lastIndex]['end']
                    )
                ) {
                    $merged[$lastIndex]['end'] =
                        $current['end'];
                }

                continue;
            }

            $merged[] = $current;
        }

        $totalMinutes = 0;

        foreach ($merged as $interval) {
            $totalMinutes += (int) $interval['start']
                ->diffInMinutes($interval['end']);
        }

        return $totalMinutes;
    }

    /**
     * Create an interval from attendance values.
     *
     * This method also safely handles TIME columns that
     * Laravel/MySQL may return as full datetime strings.
     */
    private function createAttendanceInterval(
        mixed $date,
        mixed $timeIn,
        mixed $timeOut
    ): array {
        return $this->createInterval(
            $date,
            $timeIn,
            $timeOut
        );
    }

    /**
     * Get actual attendance intervals.
     */
    private function getActualAttendanceIntervals(
        AttendanceRecord $attendanceRecord
    ): array {
        $intervals = [];

        $date = $this->normalizeDate(
            $attendanceRecord->date
        );

        $timeEntries = $attendanceRecord
            ->timeEntries()
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->get();

        foreach ($timeEntries as $entry) {
            $interval = $this->createAttendanceInterval(
                $date,
                $entry->time_in,
                $entry->time_out
            );

            if (
                $interval['end']->gt(
                    $interval['start']
                )
            ) {
                $intervals[] = $interval;
            }
        }

        if (!empty($intervals)) {
            return $intervals;
        }

        /*
         * Legacy attendance fallback.
         *
         * Do not count an OB-only attendance record as
         * actual worked attendance.
         */
        if (
            !$attendanceRecord->isOfficialBusiness()
            && $attendanceRecord->time_in
            && $attendanceRecord->time_out
        ) {
            $interval = $this->createAttendanceInterval(
                $date,
                $attendanceRecord->time_in,
                $attendanceRecord->time_out
            );

            if (
                $interval['end']->gt(
                    $interval['start']
                )
            ) {
                $intervals[] = $interval;
            }
        }

        return $intervals;
    }

    /**
     * Get approved OB intervals.
     */
    private function getApprovedObIntervals(
        string $employeeId,
        string $date
    ): array {
        $intervals = [];

        $requests = OfficialBusinessRequest::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->where(
                'status',
                OfficialBusinessRequest::APPROVED
            )
            ->whereNotNull('ob_start_time')
            ->whereNotNull('ob_end_time')
            ->get();

        foreach ($requests as $obRequest) {
            $interval = $this->createInterval(
                $date,
                $obRequest->ob_start_time,
                $obRequest->ob_end_time
            );

            if (
                !$interval['end']->gt(
                    $interval['start']
                )
            ) {
                continue;
            }

            $lunchStart = Carbon::parse(
                $date . ' 12:00:00',
                $this->timezone()
            );

            $lunchEnd = Carbon::parse(
                $date . ' 13:00:00',
                $this->timezone()
            );

            /*
             * Preserve the interval portions before and after lunch.
             * This makes 08:00-17:00 contribute 8 credited hours.
             */
            if ($interval['start']->lt($lunchStart)) {
                $beforeLunchEnd = $interval['end']->lt($lunchStart)
                    ? $interval['end']->copy()
                    : $lunchStart->copy();

                if ($beforeLunchEnd->gt($interval['start'])) {
                    $intervals[] = [
                        'start' => $interval['start']->copy(),
                        'end' => $beforeLunchEnd,
                    ];
                }
            }

            if ($interval['end']->gt($lunchEnd)) {
                $afterLunchStart = $interval['start']->gt($lunchEnd)
                    ? $interval['start']->copy()
                    : $lunchEnd->copy();

                if ($interval['end']->gt($afterLunchStart)) {
                    $intervals[] = [
                        'start' => $afterLunchStart,
                        'end' => $interval['end']->copy(),
                    ];
                }
            }

            /*
             * No lunch overlap: keep the original interval.
             */
            if (
                $interval['end']->lte($lunchStart)
                || $interval['start']->gte($lunchEnd)
            ) {
                $intervals[] = $interval;
            }
        }

        return $intervals;
    }

    /**
     * Recalculate attendance.
     *
     * Actual attendance + approved OB = credited time.
     *
     * Overlapping time is counted once.
     *
     * OB cannot generate overtime.
     */
    private function recalculateAttendanceWithOfficialBusiness(
        AttendanceRecord $attendanceRecord
    ): void {
        $attendanceRecord->refresh();

        $date = $this->normalizeDate(
            $attendanceRecord->date
        );

        $actualIntervals =
            $this->getActualAttendanceIntervals(
                $attendanceRecord
            );

        $obIntervals =
            $this->getApprovedObIntervals(
                $attendanceRecord->employee_id,
                $date
            );

        $actualMinutes = $this->calculateUniqueMinutes(
            $actualIntervals
        );

        $combinedMinutes = $this->calculateUniqueMinutes(
            array_merge(
                $actualIntervals,
                $obIntervals
            )
        );

        $actualHours = round(
            $actualMinutes / 60,
            2
        );

        $combinedHours = round(
            $combinedMinutes / 60,
            2
        );

        $regularHours = min(
            8,
            $combinedHours
        );

        /*
         * Only actual attendance creates overtime.
         */
        $overtimeHours = max(
            0,
            $actualHours - 8
        );

        $attendanceRecord->update([
            'total_hours' => $combinedHours,

            'regular_hours' => round(
                $regularHours,
                2
            ),

            'overtime_hours' => round(
                $overtimeHours,
                2
            ),
        ]);
    }
}
