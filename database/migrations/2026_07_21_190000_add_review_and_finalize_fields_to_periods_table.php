<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->timestamp('reviewed_at')->nullable()->after('ready_by');
            $table->uuid('reviewed_by')->nullable()->after('reviewed_at');
            $table->timestamp('finalized_at')->nullable()->after('reviewed_by');
            $table->uuid('finalized_by')->nullable()->after('finalized_at');
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'reviewed_at',
                'reviewed_by',
                'finalized_at',
                'finalized_by',
            ]);
        });
    }
};
