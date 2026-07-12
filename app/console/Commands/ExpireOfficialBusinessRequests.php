<?php

namespace App\Console\Commands;

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
            }

            $this->info("{$modelClass}: expired {$count} overdue request(s).");
        }

        return self::SUCCESS;
    }
}