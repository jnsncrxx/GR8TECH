<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            // Indicates whether an admin/HR/manager has explicitly configured this
            // employee's leave balance. When false, the record only tracks usage;
            // vacation, sick, and SIL are treated as uncapped and unpaid.
            $table->boolean('is_balance_set')->default(false)->after('bl_days_used');

            // When true, the SIL total stored in sil_days_total will NOT become
            // active (paid/remaining) until the employee completes one year as a
            // regular employee (regularization_date ?? hire_date).
            // The artisan command `leave:auto-grant-sil` flips this to false once
            // the anniversary passes.
            $table->boolean('sil_deferred')->default(false)->after('is_balance_set');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropColumn(['is_balance_set', 'sil_deferred']);
        });
    }
};
