<?php

namespace Tests\Feature;

use App\Http\Controllers\Concerns\CalculatesAttendanceWithOfficialBusiness;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\OfficialBusinessRequest;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers combining approved Official Business (OB) duration with actual
 * worked time as a UNIQUE union (overlaps counted once, OB never generates
 * overtime), and guards the "2026-07-14 2026-07-14 08:00:00" double-date
 * regression that came from concatenating a date with a datetime-cast OB time.
 */
class OfficialBusinessAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private const DATE = '2026-07-14';

    /**
     * Anonymous holder that exposes the trait's private methods so the
     * calculation engine can be exercised in isolation (the same engine
     * both OfficialBusinessController and TimeInOutController now use).
     */
    private function calculator(): object
    {
        return new class {
            use CalculatesAttendanceWithOfficialBusiness;

            public function recalc(AttendanceRecord $record): void
            {
                $this->recalculateAttendanceWithOfficialBusiness($record);
            }

            public function normTime(mixed $value): string
            {
                return $this->normalizeTime($value);
            }

            public function normDate(mixed $value): string
            {
                return $this->normalizeDate($value);
            }

            public function makeInterval(mixed $date, mixed $start, mixed $end): array
            {
                return $this->createInterval($date, $start, $end);
            }

            public function uniqueMinutes(array $intervals): int
            {
                return $this->calculateUniqueMinutes($intervals);
            }
        };
    }

    private function makeEmployee(): Employee
    {
        $department = Department::create([
            'department_id' => 'DEPT-OB-1',
            'name' => 'Operations',
            'budget' => 0,
        ]);

        return Employee::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'salary' => 26000,
            'department_id' => $department->id,
            'hire_date' => '2024-01-01',
        ]);
    }

    private function makeAttendance(
        Employee $employee,
        string $status = AttendanceRecord::PRESENT,
        array $attributes = []
    ): AttendanceRecord {
        return AttendanceRecord::create(array_merge([
            'employee_id' => $employee->id,
            'date' => self::DATE,
            'status' => $status,
        ], $attributes));
    }

    private function makeTimeEntry(
        AttendanceRecord $attendance,
        string $timeIn,
        string $timeOut
    ): TimeEntry {
        return TimeEntry::create([
            'attendance_record_id' => $attendance->id,
            'time_in' => self::DATE . ' ' . $timeIn,
            'time_out' => self::DATE . ' ' . $timeOut,
        ]);
    }

    private function makeOb(
        Employee $employee,
        string $start,
        string $end,
        string $status = OfficialBusinessRequest::APPROVED
    ): OfficialBusinessRequest {
        return OfficialBusinessRequest::create([
            'employee_id' => $employee->id,
            'date' => self::DATE,
            'reason' => 'Field work',
            'is_full_day' => false,
            'ob_start_time' => $start,
            'ob_end_time' => $end,
            'status' => $status,
        ]);
    }

    private function assertHours(
        AttendanceRecord $record,
        float $total,
        float $regular,
        float $overtime
    ): void {
        $record->refresh();

        $this->assertEqualsWithDelta($total, (float) $record->total_hours, 0.001, 'total_hours');
        $this->assertEqualsWithDelta($regular, (float) $record->regular_hours, 0.001, 'regular_hours');
        $this->assertEqualsWithDelta($overtime, (float) $record->overtime_hours, 0.001, 'overtime_hours');
    }

    public function test_compute_credited_hours_deducts_lunch_overlap(): void
    {
        $employee = $this->makeEmployee();

        $fullDay = $this->makeOb($employee, '08:00', '17:00');
        $this->assertEqualsWithDelta(
            8.0,
            $fullDay->computeCreditedHours(),
            0.001
        );

        $halfDay = $this->makeOb($employee, '08:00', '12:00');
        $this->assertEqualsWithDelta(
            4.0,
            $halfDay->computeCreditedHours(),
            0.001
        );

        $afternoon = $this->makeOb($employee, '13:00', '17:00');
        $this->assertEqualsWithDelta(
            4.0,
            $afternoon->computeCreditedHours(),
            0.001
        );

        $partialLunch = $this->makeOb($employee, '11:30', '12:30');
        $this->assertEqualsWithDelta(
            0.5,
            $partialLunch->computeCreditedHours(),
            0.001
        );
    }

    public function test_ob_only_full_day_deducts_standard_lunch(): void
    {
        $employee = $this->makeEmployee();

        $attendance = $this->makeAttendance(
            $employee,
            AttendanceRecord::OFFICIAL_BUSINESS
        );

        $this->makeOb($employee, '08:00', '17:00');

        $this->calculator()->recalc($attendance);

        $this->assertHours($attendance, 8.0, 8.0, 0.0);
    }

    // ---------------------------------------------------------------------
    // Attendance recalculation matrix
    // ---------------------------------------------------------------------

    public function test_actual_only(): void
    {
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee);
        $this->makeTimeEntry($attendance, '08:00:00', '12:00:00');

        $this->calculator()->recalc($attendance);

        $this->assertHours($attendance, 4.0, 4.0, 0.0);
    }

    public function test_ob_only_contributes_zero_actual_and_no_overtime(): void
    {
        $employee = $this->makeEmployee();
        // OFFICIAL_BUSINESS record with no TimeEntry and no legacy time_in/out.
        $attendance = $this->makeAttendance(
            $employee,
            AttendanceRecord::OFFICIAL_BUSINESS
        );
        $this->makeOb($employee, '13:00', '17:00');

        $this->calculator()->recalc($attendance);

        // 4 credited OB hours, but actual = 0 so no overtime.
        $this->assertHours($attendance, 4.0, 4.0, 0.0);
    }

    public function test_actual_plus_non_overlapping_ob(): void
    {
        // Requirement Example 1: 08-12 actual + 13-17 OB => 8 / 8 / 0.
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee);
        $this->makeTimeEntry($attendance, '08:00:00', '12:00:00');
        $this->makeOb($employee, '13:00', '17:00');

        $this->calculator()->recalc($attendance);

        $this->assertHours($attendance, 8.0, 8.0, 0.0);
    }

    public function test_actual_plus_overlapping_ob_counts_overlap_once(): void
    {
        // 08-12 actual + 10-14 OB, excluding the 12-13 lunch interval:
        // union 08-12 and 13-14 = 5 credited hours.
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee);
        $this->makeTimeEntry($attendance, '08:00:00', '12:00:00');
        $this->makeOb($employee, '10:00', '14:00');

        $this->calculator()->recalc($attendance);

        $this->assertHours($attendance, 5.0, 5.0, 0.0);
    }

    public function test_multiple_ob_intervals_all_participate(): void
    {
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance(
            $employee,
            AttendanceRecord::OFFICIAL_BUSINESS
        );
        $this->makeOb($employee, '08:00', '10:00');
        $this->makeOb($employee, '13:00', '15:00');

        $this->calculator()->recalc($attendance);

        // Two disjoint 2-hour OB blocks => 4 credited hours, no actual/overtime.
        $this->assertHours($attendance, 4.0, 4.0, 0.0);
    }

    public function test_actual_overtime_with_ob_inside_actual(): void
    {
        // Requirement Example 3: actual 08-18 (10h) + OB 10-12 => 10 / 8 / 2.
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee);
        $this->makeTimeEntry($attendance, '08:00:00', '18:00:00');
        $this->makeOb($employee, '10:00', '12:00');

        $this->calculator()->recalc($attendance);

        $this->assertHours($attendance, 10.0, 8.0, 2.0);
    }

    public function test_rejected_ob_is_excluded(): void
    {
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee);
        $this->makeTimeEntry($attendance, '08:00:00', '12:00:00');
        $this->makeOb($employee, '13:00', '17:00', OfficialBusinessRequest::REJECTED);

        $this->calculator()->recalc($attendance);

        // Rejected OB must not add credited hours.
        $this->assertHours($attendance, 4.0, 4.0, 0.0);
    }

    public function test_expired_ob_is_excluded(): void
    {
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee);
        $this->makeTimeEntry($attendance, '08:00:00', '12:00:00');
        $this->makeOb($employee, '13:00', '17:00', OfficialBusinessRequest::EXPIRED);

        $this->calculator()->recalc($attendance);

        $this->assertHours($attendance, 4.0, 4.0, 0.0);
    }

    public function test_legacy_time_in_out_used_only_when_no_time_entries(): void
    {
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee, AttendanceRecord::PRESENT, [
            'time_in' => self::DATE . ' 08:00:00',
            'time_out' => self::DATE . ' 12:00:00',
        ]);

        $this->calculator()->recalc($attendance);

        // No TimeEntry rows => falls back to attendance time_in/out (4h).
        $this->assertHours($attendance, 4.0, 4.0, 0.0);
    }

    // ---------------------------------------------------------------------
    // Double-date regression + normalization
    // ---------------------------------------------------------------------

    public function test_double_date_regression_recalc_does_not_throw(): void
    {
        // Reproduces the exact bug scenario: an approved OB whose time fields
        // are datetime-cast Carbon instances, recalculated for the same day.
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance(
            $employee,
            AttendanceRecord::OFFICIAL_BUSINESS
        );
        $ob = $this->makeOb($employee, '08:00', '12:00');

        // The stored value is a Carbon that stringifies with a date; this is
        // precisely what used to produce "2026-07-14 2026-07-14 08:00:00".
        $this->assertInstanceOf(Carbon::class, $ob->fresh()->ob_start_time);

        $this->calculator()->recalc($attendance);

        $this->assertHours($attendance, 4.0, 4.0, 0.0);
    }

    public function test_normalize_time_variants_all_yield_same_value(): void
    {
        $calc = $this->calculator();

        $this->assertSame('08:00:00', $calc->normTime('08:00'));
        $this->assertSame('08:00:00', $calc->normTime('08:00:00'));
        $this->assertSame('08:00:00', $calc->normTime('2026-07-14 08:00:00'));
        $this->assertSame('08:00:00', $calc->normTime('2026-07-14T08:00:00'));
        $this->assertSame('08:00:00', $calc->normTime(Carbon::parse('2026-07-14 08:00:00')));
    }

    public function test_normalize_date_variants(): void
    {
        $calc = $this->calculator();

        $this->assertSame('2026-07-14', $calc->normDate('2026-07-14'));
        $this->assertSame('2026-07-14', $calc->normDate('2026-07-14 08:00:00'));
        $this->assertSame('2026-07-14', $calc->normDate(Carbon::parse('2026-07-14 08:00:00')));
    }

    public function test_create_interval_never_double_dates(): void
    {
        $calc = $this->calculator();

        // Passing a full datetime string as the "time" must NOT prepend a
        // second date; it must resolve to the intended time on the given day.
        $interval = $calc->makeInterval(
            '2026-07-14',
            '2026-07-14 08:00:00',
            '2026-07-14 12:00:00'
        );

        $this->assertSame('2026-07-14 08:00:00', $interval['start']->format('Y-m-d H:i:s'));
        $this->assertSame('2026-07-14 12:00:00', $interval['end']->format('Y-m-d H:i:s'));
    }

    // ---------------------------------------------------------------------
    // Interval merge (unique minutes)
    // ---------------------------------------------------------------------

    public function test_unique_minutes_merges_overlapping_intervals(): void
    {
        $calc = $this->calculator();

        $minutes = $calc->uniqueMinutes([
            $calc->makeInterval(self::DATE, '08:00', '12:00'),
            $calc->makeInterval(self::DATE, '10:00', '14:00'),
        ]);

        $this->assertSame(360, $minutes); // union 08:00-14:00
    }

    public function test_unique_minutes_merges_touching_intervals(): void
    {
        $calc = $this->calculator();

        $minutes = $calc->uniqueMinutes([
            $calc->makeInterval(self::DATE, '08:00', '10:00'),
            $calc->makeInterval(self::DATE, '10:00', '12:00'),
        ]);

        $this->assertSame(240, $minutes); // union 08:00-12:00
    }

    // ---------------------------------------------------------------------
    // OB-vs-OB overlap comparison (the predicate store() uses)
    // ---------------------------------------------------------------------

    public function test_overlap_predicate_matches_store_rule(): void
    {
        $calc = $this->calculator();

        $overlaps = function (array $a, array $b): bool {
            // Same normalized-interval comparison used in store().
            return $a['start']->lt($b['end']) && $a['end']->gt($b['start']);
        };

        $new = $calc->makeInterval(self::DATE, '10:00', '12:00');

        // Genuine overlap => blocked.
        $this->assertTrue(
            $overlaps($new, $calc->makeInterval(self::DATE, '11:00', '13:00'))
        );

        // Merely touching (adjacent) => allowed.
        $this->assertFalse(
            $overlaps($new, $calc->makeInterval(self::DATE, '12:00', '14:00'))
        );

        // Disjoint => allowed.
        $this->assertFalse(
            $overlaps($new, $calc->makeInterval(self::DATE, '13:00', '15:00'))
        );
    }

    // ---------------------------------------------------------------------
    // Existing-attendance preservation
    // ---------------------------------------------------------------------

    public function test_recalculation_preserves_attendance_fields_and_time_entries(): void
    {
        $employee = $this->makeEmployee();
        $attendance = $this->makeAttendance($employee, AttendanceRecord::PRESENT, [
            'time_in' => self::DATE . ' 08:00:00',
            'time_out' => self::DATE . ' 12:00:00',
            'break_start' => self::DATE . ' 10:00:00',
            'break_end' => self::DATE . ' 10:15:00',
        ]);
        $entry = $this->makeTimeEntry($attendance, '08:00:00', '12:00:00');
        $this->makeOb($employee, '13:00', '17:00');

        $this->calculator()->recalc($attendance);
        $attendance->refresh();

        // Recalc only touches the three hour fields.
        $this->assertSame(AttendanceRecord::PRESENT, $attendance->status);
        $this->assertSame('08:00:00', $attendance->time_in->format('H:i:s'));
        $this->assertSame('12:00:00', $attendance->time_out->format('H:i:s'));
        $this->assertSame('10:00:00', $attendance->break_start->format('H:i:s'));
        $this->assertSame('10:15:00', $attendance->break_end->format('H:i:s'));

        // TimeEntry rows untouched.
        $this->assertDatabaseHas('time_entries', ['id' => $entry->id]);
        $this->assertSame(1, $attendance->timeEntries()->count());

        // And the union math still applied.
        $this->assertHours($attendance, 8.0, 8.0, 0.0);
    }
}
