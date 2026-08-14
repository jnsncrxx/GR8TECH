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
            $table->integer('spl_days_total')->default(0)->after('study_days_used');
            $table->integer('spl_days_used')->default(0)->after('spl_days_total');
            $table->integer('vawc_days_total')->default(0)->after('spl_days_used');
            $table->integer('vawc_days_used')->default(0)->after('vawc_days_total');
            $table->integer('bl_days_total')->default(0)->after('vawc_days_used');
            $table->integer('bl_days_used')->default(0)->after('bl_days_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropColumn([
                'spl_days_total',
                'spl_days_used',
                'vawc_days_total',
                'vawc_days_used',
                'bl_days_total',
                'bl_days_used',
            ]);
        });
    }
};
