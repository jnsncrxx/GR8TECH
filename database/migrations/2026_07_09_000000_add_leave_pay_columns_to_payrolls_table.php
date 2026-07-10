<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'sick_leave_days')) {
                $table->integer('sick_leave_days')->default(0)->after('bonuses');
            }

            if (!Schema::hasColumn('payrolls', 'sick_leave_pay')) {
                $table->decimal('sick_leave_pay', 10, 2)->default(0)->after('sick_leave_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'sick_leave_pay')) {
                $table->dropColumn('sick_leave_pay');
            }

            if (Schema::hasColumn('payrolls', 'sick_leave_days')) {
                $table->dropColumn('sick_leave_days');
            }
        });
    }
};
