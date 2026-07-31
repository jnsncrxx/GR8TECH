<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_infos', 'last_edited_at')) {
                $table->timestamp('last_edited_at')->nullable()->after('bank');
            }
            if (!Schema::hasColumn('employee_infos', 'last_edited_reason')) {
                $table->text('last_edited_reason')->nullable()->after('last_edited_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_infos', function (Blueprint $table) {
            $columns = array_filter(['last_edited_at', 'last_edited_reason'], function ($column) {
                return Schema::hasColumn('employee_infos', $column);
            });

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
