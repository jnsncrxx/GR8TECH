<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('official_business_requests', function (Blueprint $table) {
            $table->dropColumn(['reviewed_by', 'created_by']);
        });

        Schema::table('official_business_requests', function (Blueprint $table) {
            $table->uuid('reviewed_by')->nullable()->after('rejection_reason');
            $table->uuid('created_by')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('official_business_requests', function (Blueprint $table) {
            $table->dropColumn(['reviewed_by', 'created_by']);
        });

        Schema::table('official_business_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
        });
    }
};
