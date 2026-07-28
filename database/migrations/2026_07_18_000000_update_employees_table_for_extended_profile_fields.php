<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Add the new columns.
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('employees', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('employees', 'sex')) {
                $table->string('sex')->nullable()->after('civil_status');
            }
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable()->after('sex');
            }
            if (!Schema::hasColumn('employees', 'emergency_address')) {
                $table->text('emergency_address')->nullable()->after('emergency_relationship');
            }
        });

        // 2) Backfill the new merged address columns from the old split ones,
        //    if the old columns still exist.
        if (Schema::hasColumn('employees', 'home_address') || Schema::hasColumn('employees', 'current_address')) {
            $homeCol = Schema::hasColumn('employees', 'home_address') ? 'home_address' : null;
            $currentCol = Schema::hasColumn('employees', 'current_address') ? 'current_address' : null;

            DB::table('employees')->orderBy('id')->chunk(200, function ($employees) use ($homeCol, $currentCol) {
                foreach ($employees as $employee) {
                    $home = $homeCol ? $employee->$homeCol : null;
                    $current = $currentCol ? $employee->$currentCol : null;
                    $merged = trim(collect([$current, $home])->filter()->unique()->implode(' / '));

                    if ($merged !== '') {
                        DB::table('employees')->where('id', $employee->id)->update(['address' => $merged]);
                    }
                }
            });
        }

        if (Schema::hasColumn('employees', 'emergency_home_address') || Schema::hasColumn('employees', 'emergency_current_address')) {
            $homeCol = Schema::hasColumn('employees', 'emergency_home_address') ? 'emergency_home_address' : null;
            $currentCol = Schema::hasColumn('employees', 'emergency_current_address') ? 'emergency_current_address' : null;

            DB::table('employees')->orderBy('id')->chunk(200, function ($employees) use ($homeCol, $currentCol) {
                foreach ($employees as $employee) {
                    $home = $homeCol ? $employee->$homeCol : null;
                    $current = $currentCol ? $employee->$currentCol : null;
                    $merged = trim(collect([$current, $home])->filter()->unique()->implode(' / '));

                    if ($merged !== '') {
                        DB::table('employees')->where('id', $employee->id)->update(['emergency_address' => $merged]);
                    }
                }
            });
        }

        // 3) Drop the columns that are no longer collected by the form.
        //    Note: facebook_link is intentionally kept — it's still used
        //    in the Additional Employee Details section.
        Schema::table('employees', function (Blueprint $table) {
            foreach (['home_address', 'current_address', 'emergency_home_address', 'emergency_current_address', 'emergency_facebook_link'] as $column) {
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
            if (!Schema::hasColumn('employees', 'home_address')) {
                $table->text('home_address')->nullable();
            }
            if (!Schema::hasColumn('employees', 'current_address')) {
                $table->text('current_address')->nullable();
            }
            if (!Schema::hasColumn('employees', 'emergency_home_address')) {
                $table->text('emergency_home_address')->nullable();
            }
            if (!Schema::hasColumn('employees', 'emergency_current_address')) {
                $table->text('emergency_current_address')->nullable();
            }
            if (!Schema::hasColumn('employees', 'emergency_facebook_link')) {
                $table->string('emergency_facebook_link')->nullable();
            }
        });

        DB::table('employees')->orderBy('id')->chunk(200, function ($employees) {
            foreach ($employees as $employee) {
                if (!empty($employee->address)) {
                    DB::table('employees')->where('id', $employee->id)->update(['current_address' => $employee->address]);
                }
                if (!empty($employee->emergency_address)) {
                    DB::table('employees')->where('id', $employee->id)->update(['emergency_current_address' => $employee->emergency_address]);
                }
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $columns = ['photo_path', 'middle_name', 'sex', 'address', 'emergency_address'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
