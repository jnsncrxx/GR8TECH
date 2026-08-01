<?php

namespace Tests\Unit;

use App\Http\Controllers\Web\ScheduleV2Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ReflectionMethod;
use Tests\TestCase;

class ScheduleV2ControllerValidationTest extends TestCase
{
    private function rules(array $input): array
    {
        $method = new ReflectionMethod(ScheduleV2Controller::class, 'scheduleDetailRules');
        $method->setAccessible(true);

        return $method->invoke(new ScheduleV2Controller(), Request::create('/', 'POST', $input));
    }

    private function normalize(array $validated): array
    {
        $method = new ReflectionMethod(ScheduleV2Controller::class, 'normalizedScheduleDetails');
        $method->setAccessible(true);

        return $method->invoke(new ScheduleV2Controller(), $validated);
    }

    public function test_fixed_workday_requires_start_and_end_times(): void
    {
        $input = ['status' => 'Working', 'schedule_type' => 'fixed'];

        $validator = Validator::make($input, $this->rules($input));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('time_in', $validator->errors()->toArray());
        $this->assertArrayHasKey('time_out', $validator->errors()->toArray());
    }

    public function test_legacy_submission_defaults_to_fixed_schedule(): void
    {
        $input = [
            'status' => 'Working',
            'time_in' => '08:00',
            'time_out' => '17:00',
        ];
        $request = Request::create('/', 'POST', $input);
        $method = new ReflectionMethod(ScheduleV2Controller::class, 'scheduleDetailRules');
        $method->setAccessible(true);
        $rules = $method->invoke(new ScheduleV2Controller(), $request);

        $this->assertSame('fixed', $request->input('schedule_type'));
        $this->assertFalse(Validator::make($request->all(), $rules)->fails());
    }

    public function test_custom_fixed_schedule_is_valid_and_derives_net_required_hours(): void
    {
        $input = [
            'status' => 'Working',
            'schedule_type' => 'fixed',
            'time_in' => '07:30',
            'time_out' => '16:30',
        ];

        $this->assertFalse(Validator::make($input, $this->rules($input))->fails());

        $details = $this->normalize($input);
        $this->assertSame('fixed', $details['schedule_type']);
        $this->assertSame(8.0, $details['required_hours']);
        $this->assertSame('07:30', $details['time_in']);
        $this->assertSame('16:30', $details['time_out']);
    }

    public function test_fixed_schedule_rejects_end_time_before_start_time(): void
    {
        $input = [
            'status' => 'Working',
            'schedule_type' => 'fixed',
            'time_in' => '17:00',
            'time_out' => '08:00',
        ];

        $this->assertTrue(Validator::make($input, $this->rules($input))->fails());
    }

    public function test_flexible_workday_requires_hours_and_discards_fixed_times(): void
    {
        $missingHours = ['status' => 'Working', 'schedule_type' => 'flexible'];
        $this->assertTrue(Validator::make($missingHours, $this->rules($missingHours))->fails());

        $details = $this->normalize([
            'status' => 'Working',
            'schedule_type' => 'flexible',
            'required_hours' => 7.5,
            'time_in' => '08:00',
            'time_out' => '17:00',
        ]);

        $this->assertSame('flexible', $details['schedule_type']);
        $this->assertSame(7.5, $details['required_hours']);
        $this->assertNull($details['time_in']);
        $this->assertNull($details['time_out']);
    }

    public function test_non_working_day_carries_no_payable_hours_or_shift_times(): void
    {
        $details = $this->normalize([
            'status' => 'Day Off',
            'schedule_type' => 'fixed',
            'time_in' => '08:00',
            'time_out' => '17:00',
        ]);

        $this->assertSame(0, $details['required_hours']);
        $this->assertNull($details['time_in']);
        $this->assertNull($details['time_out']);
    }

    public function test_official_business_schedule_carries_no_manual_shift_hours(): void
    {
        $details = $this->normalize([
            'status' => 'Official Business',
            'schedule_type' => 'fixed',
            'time_in' => '08:00',
            'time_out' => '17:00',
        ]);

        $this->assertSame(0, $details['required_hours']);
        $this->assertNull($details['time_in']);
        $this->assertNull($details['time_out']);
    }
}
