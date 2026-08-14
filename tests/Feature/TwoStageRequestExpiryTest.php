<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\OfficialBusinessRequest;
use App\Notifications\RequestStatusChanged;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoStageRequestExpiryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_request_can_only_be_resubmitted_once_for_a_second_24_hour_window(): void
    {
        Carbon::setTestNow('2026-08-10 08:00:00');

        $department = Department::create([
            'department_id' => 'DEPT-EXPIRY',
            'name' => 'Operations',
            'budget' => 0,
        ]);

        $employee = Employee::create([
            'first_name' => 'Expiry',
            'last_name' => 'Tester',
            'salary' => 26000,
            'department_id' => $department->id,
            'hire_date' => '2026-01-01',
        ]);

        $request = OfficialBusinessRequest::create([
            'employee_id' => $employee->id,
            'date' => '2026-08-10',
            'reason' => 'Client visit',
            'is_full_day' => false,
            'ob_start_time' => '08:00',
            'ob_end_time' => '12:00',
            'status' => OfficialBusinessRequest::PENDING,
        ]);

        $this->assertSame(1, $request->expiry_attempt);
        $this->assertTrue($request->expires_at->equalTo(now()->addHours(24)));

        Carbon::setTestNow('2026-08-11 08:00:00');
        $this->assertTrue($request->expireCurrentWindow());
        $this->assertTrue($request->canBeResubmitted());
        $this->assertNotNull($request->first_expired_at);
        $this->assertNull($request->final_expired_at);

        $this->assertTrue($request->resubmitForFinalWindow());
        $this->assertSame(OfficialBusinessRequest::PENDING, $request->status);
        $this->assertSame(2, $request->expiry_attempt);
        $this->assertNotNull($request->resubmitted_at);
        $this->assertTrue($request->expires_at->equalTo(now()->addHours(24)));

        Carbon::setTestNow('2026-08-12 08:00:00');
        $this->assertTrue($request->expireCurrentWindow());
        $this->assertTrue($request->isFinallyExpired());
        $this->assertNotNull($request->final_expired_at);
        $this->assertFalse($request->canBeResubmitted());
        $this->assertFalse($request->resubmitForFinalWindow());
    }

    public function test_request_notifications_use_relative_personal_request_urls(): void
    {
        $cases = [
            RequestStatusChanged::TYPE_LEAVE => '/leave-management?scope=mine',
            RequestStatusChanged::TYPE_OVERTIME => '/overtime?scope=mine',
            RequestStatusChanged::TYPE_OFFICIAL_BUSINESS => '/official-business?scope=mine',
        ];

        foreach ($cases as $requestType => $expectedUrl) {
            $notification = new RequestStatusChanged(
                requestType: $requestType,
                requestId: 'request-id',
                status: 'expired',
                dateLabel: 'Aug 10, 2026',
            );

            $this->assertSame($expectedUrl, $notification->toArray((object) [])['url']);
        }
    }
}
