<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->uuid('company_id')->nullable()->after('period_id')->index();
            $table->decimal('worked_hours', 8, 2)->default(0)->after('scheduled_hours');
            $table->unsignedInteger('late_minutes')->default(0)->after('worked_hours');
            $table->unsignedInteger('undertime_minutes')->default(0)->after('late_minutes');
            $table->decimal('late_deduction', 10, 2)->default(0)->after('undertime_minutes');
            $table->decimal('undertime_deduction', 10, 2)->default(0)->after('late_deduction');
            $table->decimal('absence_deduction', 10, 2)->default(0)->after('undertime_deduction');
            $table->decimal('other_earnings', 10, 2)->default(0)->after('bonuses');
            $table->decimal('other_deductions', 10, 2)->default(0)->after('deductions');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropColumn([
                'company_id',
                'worked_hours',
                'late_minutes',
                'undertime_minutes',
                'late_deduction',
                'undertime_deduction',
                'absence_deduction',
                'other_earnings',
                'other_deductions',
            ]);
        });
    }
};
