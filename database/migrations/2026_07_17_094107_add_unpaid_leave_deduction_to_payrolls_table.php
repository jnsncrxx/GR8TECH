<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'unpaid_leave_deduction')) {
                $table->decimal('unpaid_leave_deduction', 10, 2)
                    ->default(0)
                    ->after('tax_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'unpaid_leave_deduction')) {
                $table->dropColumn('unpaid_leave_deduction');
            }
        });
    }
};