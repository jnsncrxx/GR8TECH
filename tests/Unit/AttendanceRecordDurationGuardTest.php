<?php

namespace Tests\Unit;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AttendanceRecordDurationGuardTest extends TestCase
{
    public function test_valid_corrected_shift_is_calculated_normally(): void
    {
        $record = new AttendanceRecord([
            'date' => '2026-07-20',
            'time_in' => '2026-07-20 08:00:00',
            'time_out' => '2026-07-20 18:15:00',
            'corrected_at' => Carbon::parse('2026-07-20 19:00:00'),
        ]);
        $record->setRelation('breaks', new Collection());

        $this->assertEqualsWithDelta(10.25, $record->calculateTotalHours(), 0.001);
    }

    public function test_cross_date_corruption_cannot_create_multi_day_worked_hours(): void
    {
        $record = new AttendanceRecord([
            'date' => '2026-07-20',
            'time_in' => '2026-07-14 08:00:00',
            'time_out' => '2026-07-20 18:15:00',
            'corrected_at' => Carbon::parse('2026-07-20 19:00:00'),
        ]);
        $record->setRelation('breaks', new Collection());

        $this->assertSame(0.0, $record->calculateTotalHours());
    }
}
