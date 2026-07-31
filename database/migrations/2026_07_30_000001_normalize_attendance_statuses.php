<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->string('validation_status', 30)
                ->default('valid')
                ->after('status')
                ->index();
        });

        DB::table('attendance_records')
            ->orderBy('id')
            ->chunk(200, function ($records) {
                foreach ($records as $record) {
                    $timeIn = $record->time_in ? Carbon::parse($record->time_in) : null;
                    $timeOut = $record->time_out ? Carbon::parse($record->time_out) : null;
                    $validationStatus = 'valid';
                    $status = $record->status;

                    if (($timeIn && !$timeOut) || (!$timeIn && $timeOut)) {
                        $validationStatus = 'incomplete_log';
                    } elseif ($timeIn && $timeOut && (!$timeOut->gt($timeIn) || $timeIn->diffInMinutes($timeOut) > 1440)) {
                        $validationStatus = 'invalid_duration';
                        $status = 'error';
                    }

                    if ($status === 'completed') {
                        if (!$timeIn && !$timeOut) {
                            $status = 'absent';
                        } elseif (!$timeIn || !$timeOut) {
                            $status = $timeIn ? 'present' : 'absent';
                        } else {
                            $workedHours = $timeIn->diffInMinutes($timeOut) / 60;
                            $status = $workedHours < 4 ? 'half_day' : 'present';

                            $schedule = DB::table('employee_schedules')
                                ->where('employee_id', $record->employee_id)
                                ->whereDate('date', Carbon::parse($record->date)->toDateString())
                                ->first();

                            if ($status === 'present' && $schedule?->time_in) {
                                $scheduledIn = Carbon::parse(
                                    Carbon::parse($record->date)->toDateString().' '.$schedule->time_in
                                );

                                if ($timeIn->gt($scheduledIn)) {
                                    $status = 'late';
                                }
                            }
                        }
                    }

                    DB::table('attendance_records')
                        ->where('id', $record->id)
                        ->update([
                            'status' => $status,
                            'validation_status' => $validationStatus,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex(['validation_status']);
            $table->dropColumn('validation_status');
        });
    }
};
