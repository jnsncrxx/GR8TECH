<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The `address` / `emergency_address` columns were introduced by an
     * earlier migration that merged the split address fields, but the
     * Employee model, EmployeeController, and the Additional Employee
     * Details / Emergency Contact forms were never updated to match —
     * they still read/write `home_address`, `current_address`,
     * `emergency_home_address`, `emergency_current_address`, and
     * `emergency_facebook_link`. This migration brings the schema back
     * in line with what the application actually uses.
     */
    public function up(): void
    {
        // 1) Re-add the split columns the app expects.
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'home_address')) {
                $table->text('home_address')->nullable()->after('civil_status');
            }
            if (!Schema::hasColumn('employees', 'current_address')) {
                $table->text('current_address')->nullable()->after('home_address');
            }
            if (!Schema::hasColumn('employees', 'emergency_home_address')) {
                $table->text('emergency_home_address')->nullable()->after('emergency_relationship');
            }
            if (!Schema::hasColumn('employees', 'emergency_current_address')) {
                $table->text('emergency_current_address')->nullable()->after('emergency_home_address');
            }
            if (!Schema::hasColumn('employees', 'emergency_facebook_link')) {
                $table->string('emergency_facebook_link')->nullable()->after('emergency_email');
            }
        });

        // 2) Backfill the split columns from the merged ones before dropping them.
        if (Schema::hasColumn('employees', 'address')) {
            DB::table('employees')->whereNotNull('address')->orderBy('id')->chunk(200, function ($employees) {
                foreach ($employees as $employee) {
                    DB::table('employees')->where('id', $employee->id)->update([
                        'current_address' => $employee->address,
                    ]);
                }
            });
        }

        if (Schema::hasColumn('employees', 'emergency_address')) {
            DB::table('employees')->whereNotNull('emergency_address')->orderBy('id')->chunk(200, function ($employees) {
                foreach ($employees as $employee) {
                    DB::table('employees')->where('id', $employee->id)->update([
                        'emergency_current_address' => $employee->emergency_address,
                    ]);
                }
            });
        }

        // 3) Drop the merged columns now that their data has been migrated.
        Schema::table('employees', function (Blueprint $table) {
            foreach (['address', 'emergency_address'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('employees', 'emergency_address')) {
                $table->text('emergency_address')->nullable();
            }
        });

        DB::table('employees')->orderBy('id')->chunk(200, function ($employees) {
            foreach ($employees as $employee) {
                $merged = trim(collect([$employee->current_address ?? null, $employee->home_address ?? null])->filter()->unique()->implode(' / '));
                if ($merged !== '') {
                    DB::table('employees')->where('id', $employee->id)->update(['address' => $merged]);
                }

                $emergencyMerged = trim(collect([$employee->emergency_current_address ?? null, $employee->emergency_home_address ?? null])->filter()->unique()->implode(' / '));
                if ($emergencyMerged !== '') {
                    DB::table('employees')->where('id', $employee->id)->update(['emergency_address' => $emergencyMerged]);
                }
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            foreach (['home_address', 'current_address', 'emergency_home_address', 'emergency_current_address', 'emergency_facebook_link'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
