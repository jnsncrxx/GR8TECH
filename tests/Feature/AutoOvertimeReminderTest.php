<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\OvertimeReminder;
use App\Models\OvertimeRequest;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoOvertimeReminderTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $employee;
    protected Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
        \Illuminate\Support\Facades\URL::forceRootUrl('http://localhost');

        $company = \App\Models\Company::create([
            'name' => 'GR8Tech',
            'code' => 'GR8',
            'legal_name' => 'GR8Tech Inc',
            'tax_id' => '123-456-789',
        ]);

        $dept = \App\Models\Department::create([
            'name' => 'Engineering',
            'code' => 'ENG',
            'company_id' => $company->id,
            'budget' => 100000.00,
        ]);

        $pos = \App\Models\Position::create([
            'name' => 'Developer',
            'code' => 'DEV',
            'department_id' => $dept->id,
        ]);

        $this->employee = Employee::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'employee_id' => 'EMP-1001',
            'company_id' => $company->id,
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'hire_date' => '2025-01-01',
            'employment_status' => 'regular',
            'salary' => 25000.00,
        ]);

        $this->account = Account::create([
            'employee_id' => $this->employee->id,
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);
    }

    public function test_clock_out_without_overtime_does_not_create_reminder()
    {
        $now = Carbon::parse('2026-07-28 17:00:00');
        Carbon::setTestNow($now);
        $date = Carbon::today();
        
        $attendance = AttendanceRecord::create([
            'employee_id' => $this->employee->id,
            'date' => $date,
            'time_in' => $date->copy()->setHour(9),
            'status' => 'present',
        ]);

        $entry = TimeEntry::create([
            'attendance_record_id' => $attendance->id,
            'time_in' => $date->copy()->setHour(9),
            'entry_type' => 'regular',
        ]);

        $response = $this->actingAs($this->account)
            ->postJson(route('attendance.time-out'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'overtime_detected' => false,
        ]);

        $this->assertDatabaseMissing('overtime_reminders', [
            'employee_id' => $this->employee->id,
            'date' => $date->toDateString(),
        ]);
    }

    public function test_clock_out_with_extra_hours_creates_pending_reminder()
    {
        $now = Carbon::parse('2026-07-28 19:30:00');
        Carbon::setTestNow($now);
        $date = Carbon::today();

        $attendance = AttendanceRecord::create([
            'employee_id' => $this->employee->id,
            'date' => $date,
            'time_in' => $date->copy()->setHour(8),
            'status' => 'present',
        ]);

        $entry = TimeEntry::create([
            'attendance_record_id' => $attendance->id,
            'time_in' => $date->copy()->setHour(8),
            'entry_type' => 'regular',
        ]);

        $response = $this->actingAs($this->account)
            ->postJson(route('attendance.time-out'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'overtime_detected' => true,
        ]);

        $this->assertDatabaseHas('overtime_reminders', [
            'employee_id' => $this->employee->id,
            'status' => OvertimeReminder::PENDING,
        ]);
    }

    public function test_quick_submit_overtime_request_creates_ot_request_and_updates_reminder()
    {
        $date = Carbon::parse('2026-07-28');

        $attendance = AttendanceRecord::create([
            'employee_id' => $this->employee->id,
            'date' => $date,
            'time_in' => $date->copy()->setHour(8),
            'time_out' => $date->copy()->setHour(19)->setMinute(30),
            'total_hours' => 9.50,
            'status' => 'completed',
        ]);

        $reminder = OvertimeReminder::create([
            'employee_id' => $this->employee->id,
            'attendance_record_id' => $attendance->id,
            'date' => $date,
            'required_hours' => 8.00,
            'worked_hours' => 9.50,
            'extra_hours' => 1.50,
            'start_time' => $date->copy()->setHour(16),
            'end_time' => $date->copy()->setHour(17)->setMinute(30),
            'status' => OvertimeReminder::PENDING,
        ]);

        $response = $this->actingAs($this->account)
            ->postJson(route('attendance.overtime.quick-submit'), [
                'reminder_id' => $reminder->id,
                'date' => $date->toDateString(),
                'extra_hours' => 1.50,
                'start_time' => '16:00',
                'end_time' => '17:30',
                'reason' => 'Auto-detected rendered overtime',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('overtime_requests', [
            'employee_id' => $this->employee->id,
            'hours' => 1.50,
            'status' => OvertimeRequest::PENDING,
        ]);

        $this->assertEquals(OvertimeReminder::SUBMITTED, $reminder->fresh()->status);
    }

    public function test_dismiss_reminder_updates_status()
    {
        $date = Carbon::parse('2026-07-28');

        $reminder = OvertimeReminder::create([
            'employee_id' => $this->employee->id,
            'date' => $date,
            'required_hours' => 8.00,
            'worked_hours' => 9.50,
            'extra_hours' => 1.50,
            'status' => OvertimeReminder::PENDING,
        ]);

        $response = $this->actingAs($this->account)
            ->postJson(route('attendance.overtime.dismiss-reminder', ['id' => $reminder->id]));

        $response->assertStatus(200);
        $this->assertEquals(OvertimeReminder::DISMISSED, $reminder->fresh()->status);
    }

    public function test_pending_reminders_returned_in_attendance_status()
    {
        $date = Carbon::parse('2026-07-27');

        $reminder = OvertimeReminder::create([
            'employee_id' => $this->employee->id,
            'date' => $date,
            'required_hours' => 8.00,
            'worked_hours' => 10.00,
            'extra_hours' => 2.00,
            'status' => OvertimeReminder::PENDING,
        ]);

        $response = $this->actingAs($this->account)
            ->getJson(route('attendance.status'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $reminder->id,
            'extra_hours' => 2.0,
        ]);
    }
}
