<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->timestamp('unlocked_at')->nullable()->after('locked_by');
            $table->uuid('unlocked_by')->nullable()->after('unlocked_at');

            $table->timestamp('reopened_at')->nullable()->after('unlocked_by');
            $table->uuid('reopened_by')->nullable()->after('reopened_at');
            $table->text('reopen_reason')->nullable()->after('reopened_by');
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'unlocked_at',
                'unlocked_by',
                'reopened_at',
                'reopened_by',
                'reopen_reason',
            ]);
        });
    }
};
