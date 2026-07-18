<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Standard Laravel "database notifications" table. The Account model already
 * uses the Notifiable trait, so once this table exists, $account->notify(...),
 * $account->notifications, and $account->unreadNotifications all work with
 * zero extra wiring.
 *
 * Used for: Leave / Overtime / Official Business request status-change
 * notifications (Pending -> Approved / Rejected / Expired). See
 * app/Notifications/RequestStatusChanged.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->uuidMorphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
