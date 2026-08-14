<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const COMPANY_NAME = 'GR8 TECH ENTERPRISE INC.';
    private const COMPANY_CODE = 'GR8TECH';

    public function up(): void
    {
        $company = DB::table('companies')
            ->where('code', self::COMPANY_CODE)
            ->orWhere('name', self::COMPANY_NAME)
            ->first();

        // Earlier project versions created "Eternal Bright" as the default.
        // Reuse that record so all existing foreign keys remain valid.
        if (!$company) {
            $company = DB::table('companies')
                ->where('name', 'like', '%Eternal Bright%')
                ->first();
        }

        if ($company) {
            DB::table('companies')
                ->where('id', $company->id)
                ->update([
                    'name' => self::COMPANY_NAME,
                    'code' => self::COMPANY_CODE,
                    'description' => 'Primary company for the GR8TECH HRIS and Payroll System',
                    'is_active' => true,
                    'updated_at' => now(),
                ]);

            $companyId = $company->id;
        } else {
            $companyId = (string) Str::uuid();

            DB::table('companies')->insert([
                'id' => $companyId,
                'name' => self::COMPANY_NAME,
                'code' => self::COMPANY_CODE,
                'description' => 'Primary company for the GR8TECH HRIS and Payroll System',
                'country' => 'Philippines',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // This project is configured as a single-company installation.
        DB::table('companies')
            ->where('id', '<>', $companyId)
            ->update(['is_active' => false, 'updated_at' => now()]);

        foreach (['departments', 'employees', 'positions', 'periods', 'payroll_templates'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                DB::table($table)
                    ->whereNull('company_id')
                    ->update(['company_id' => $companyId]);
            }
        }
    }

    public function down(): void
    {
        // Company data is intentionally preserved. Rolling this migration back
        // must not orphan employees, departments, positions, or payroll periods.
    }
};
