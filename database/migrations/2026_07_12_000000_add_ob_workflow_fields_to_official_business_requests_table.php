<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('official_business_requests', function (Blueprint $table) {
            // Snapshot of the cutoff period this request was filed against, e.g.
            // "2026-07-11_2026-07-25". Stored at filing time (via CutoffPeriodService)
            // so history stays accurate even if cutoff config changes later.
            $table->string('cutoff_period_key', 40)->nullable()->after('date');

            // Hard deadline for approval = end of the request's cutoff period + grace
            // period (default 24h, see config/attendance_cutoff.php). The Phase 3
            // scheduled command sweeps pending requests past this timestamp to
            // 'expired'. Nullable for now — populated once Phase 2 wires in
            // CutoffPeriodService at store() time.
            $table->timestamp('expires_at')->nullable()->after('cutoff_period_key');

            // Which reviewer tier actually acted (manager | hr | admin). Manager is
            // the primary approver, HR/Admin is secondary/backup — both can see and
            // act on any pending request, so this is a record of who acted, not a
            // sequential gate. Derived from the reviewer's role at review time.
            $table->string('approved_by_role', 20)->nullable()->after('reviewed_by');

            // Supports the Phase 3 expiry sweep: "find pending requests whose
            // deadline has passed" needs to scan this efficiently.
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('official_business_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'expires_at']);
            $table->dropColumn(['cutoff_period_key', 'expires_at', 'approved_by_role']);
        });
    }
};
