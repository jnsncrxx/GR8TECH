<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Semi-monthly cutoff days (day-of-month). Defaults to the common
            // 10th/25th pattern but is configurable per company, not hardcoded.
            $table->unsignedTinyInteger('cutoff_day_1')->default(10)->after('is_active');
            $table->unsignedTinyInteger('cutoff_day_2')->default(25)->after('cutoff_day_1');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['cutoff_day_1', 'cutoff_day_2']);
        });
    }
};
