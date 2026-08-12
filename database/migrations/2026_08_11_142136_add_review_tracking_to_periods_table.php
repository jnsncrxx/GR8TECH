<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            // When the manager was notified that payroll is ready for review,
            // and the 48-hour grace/review window this notification opened.
            // Distinct from reviewed_at (when the manager actually acted) -
            // review_notified_at marks the start of the window,
            // review_expires_at marks its end, reviewed_at (existing column)
            // marks if/when it was actually reviewed within that window.   
            $table->timestamp('review_notified_at')->nullable()->after('reviewed_by');
            $table->timestamp('review_expires_at')->nullable()->after('review_notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn(['review_notified_at', 'review_expires_at']);
        });
    }
};