<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    private const TABLES = [
        'overtime_requests',
        'leave_requests',
        'official_business_requests',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedTinyInteger('expiry_attempt')->default(1)->after('expires_at');
                $table->timestamp('first_expired_at')->nullable()->after('expiry_attempt');
                $table->timestamp('resubmitted_at')->nullable()->after('first_expired_at');
                $table->timestamp('final_expired_at')->nullable()->after('resubmitted_at');
                $table->index(['status', 'expiry_attempt', 'expires_at']);
            });

            // Historical expired requests predate the two-stage workflow. Treat
            // them as final so the migration does not unexpectedly reopen old filings.
            DB::table($tableName)
                ->where('status', 'expired')
                ->update([
                    'expiry_attempt' => 2,
                    'final_expired_at' => DB::raw('COALESCE(updated_at, CURRENT_TIMESTAMP)'),
                ]);

            // Normalize requests that were still pending at deployment. Older
            // code tied expires_at to a payroll cutoff, which could leave a
            // filing pending for longer than the new fixed 24-hour window.
            DB::table($tableName)
                ->where('status', 'pending')
                ->select(['id', 'created_at'])
                ->orderBy('id')
                ->each(function (object $request) use ($tableName) {
                    $filedAt = $request->created_at
                        ? Carbon::parse($request->created_at)
                        : Carbon::now();

                    DB::table($tableName)
                        ->where('id', $request->id)
                        ->update([
                            'expiry_attempt' => 1,
                            'expires_at' => $filedAt->addHours(24),
                        ]);
                });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['status', 'expiry_attempt', 'expires_at']);
                $table->dropColumn([
                    'expiry_attempt',
                    'first_expired_at',
                    'resubmitted_at',
                    'final_expired_at',
                ]);
            });
        }
    }
};
