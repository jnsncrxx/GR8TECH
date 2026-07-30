<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('periods', 'validation_results')) {
            Schema::table('periods', function (Blueprint $table) {
                $table->json('validation_results')->nullable()->after('validation_notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('periods', 'validation_results')) {
            Schema::table('periods', function (Blueprint $table) {
                $table->dropColumn('validation_results');
            });
        }
    }
};
