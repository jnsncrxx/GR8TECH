<?php

namespace App\Console\Commands;

use App\Notifications\RequestStatusChanged;
use Illuminate\Console\Command;

class ExpireOfficialBusinessRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * NOTE: kept as `ob:expire-overdue` for backward compatibility with the
     * existing schedule entry in routes/console.php. The command itself now
     * sweeps every model listed in config/expirable_requests.php, not just
     * OB — but since that config currently only lists OfficialBusinessRequest,
     * behavior is unchanged. Rename both the signature and the scheduled
     * entry together if this ever needs a more generic name.
     */
    protected $signature = 'ob:expire-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Mark pending requests as expired once their grace deadline (expires_at) has passed, for every model listed in config/expirable_requests.php.';

    public function handle(): int
    {
        $modelClasses = config('expirable_requests.models', []);

        foreach ($modelClasses as $modelClass) {
            $overdueRequests = $modelClass::pastDeadline()->get();
            $count = $overdueRequests->count();

            foreach ($overdueRequests as $request) {
                $request->update([
                    'status' => $modelClass::EXPIRED,
                ]);

                $this->notifyRequester($request);
            }

            $this->info("{$modelClass}: expired {$count} overdue request(s).");
        }

        return self::SUCCESS;
    }

    /**
     * Notify the requesting employee's account that their request expired.
     * Mirrors the per-controller notifyRequester() helpers used on
     * approve/reject, so status changes are always announced the same way
     * regardless of whether a human or this sweep made the change.
     */
    private function notifyRequester(object $request): void
    {
        $account = $request->employee?->account;
        if (!$account) {
            return;
        }

        [$type, $dateLabel] = match (get_class($request)) {
            \App\Models\LeaveRequest::class => [
                RequestStatusChanged::TYPE_LEAVE,
                \Carbon\Carbon::parse($request->start_date)->format('M d, Y')
                    . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('M d, Y'),
            ],
            \App\Models\OvertimeRequest::class => [
                RequestStatusChanged::TYPE_OVERTIME,
                \Carbon\Carbon::parse($request->date)->format('M d, Y'),
            ],
            \App\Models\OfficialBusinessRequest::class => [
                RequestStatusChanged::TYPE_OFFICIAL_BUSINESS,
                \Carbon\Carbon::parse($request->date)->format('M d, Y'),
            ],
            default => [null, null],
        };

        if (!$type) {
            return;
        }

        $account->notify(new RequestStatusChanged(
            $type,
            $request->id,
            $request->status,
            $dateLabel,
        ));
    }
}