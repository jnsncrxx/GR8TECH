<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('companies')) {
            return;
        }

        DB::table('companies')->update([
            'is_active' => true,
            'updated_at' => now(),
        ]);

        DB::table('companies')
            ->where('code', 'GR8TECH')
            ->orWhere('name', 'GR8 TECH ENTERPRISE INC.')
            ->update([
                'name' => 'GR8 TECH ENTERPRISE INC.',
                'code' => 'GR8TECH',
                'is_active' => true,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Preserve company availability during rollback.
    }
};
