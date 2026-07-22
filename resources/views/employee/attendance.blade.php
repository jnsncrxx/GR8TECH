@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'attendance.my'])

@section('title', 'My Attendance')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Attendance</h1>
            <p class="mt-1 text-sm text-gray-600">Your assigned schedule and recorded working time</p>
        </div>
        <form method="GET" action="{{ route('attendance.my') }}" class="flex items-end gap-2">
            <div>
                <label for="month" class="block text-xs font-medium text-gray-600">Month</label>
                <input id="month" name="month" type="month" value="{{ $month }}" class="mt-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900">
            </div>
            <button class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">Load</button>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4"><p class="text-sm text-gray-500">Credited Hours</p><p class="mt-1 text-2xl font-bold">{{ \App\Helpers\TimezoneHelper::formatHours($summary['total_hours']) }}</p></div>
        <div class="rounded-xl border border-green-200 bg-green-50 p-4"><p class="text-sm text-green-700">Present</p><p class="mt-1 text-2xl font-bold text-green-900">{{ $summary['present'] }}</p></div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4"><p class="text-sm text-amber-700">Late</p><p class="mt-1 text-2xl font-bold text-amber-900">{{ $summary['late'] }}</p></div>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4"><p class="text-sm text-red-700">Recorded Absence</p><p class="mt-1 text-2xl font-bold text-red-900">{{ $summary['absent'] }}</p></div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Date</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Assigned Schedule</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Actual Log</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Credited Hours</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($records as $record)
                        @php
                            $schedule = $record->getRelation('assignedSchedule');
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-medium text-gray-900">{{ $record->date->format('M j, Y') }}</td>
                            <td class="px-5 py-4">
                                @if($schedule && $schedule->time_in && $schedule->time_out)
                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($schedule->time_in)->format('g:i A') }}–{{ \Carbon\Carbon::parse($schedule->time_out)->format('g:i A') }}</span>
                                    <span class="block text-xs text-gray-500">{{ $schedule->status_label }}</span>
                                @else
                                    <span class="{{ $schedule ? 'text-gray-600' : 'text-red-600' }}">{{ $schedule?->status_label ?? 'Missing schedule' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-700">{{ $record->time_in ? $record->time_in->format('g:i A') : '—' }}–{{ $record->time_out ? $record->time_out->format('g:i A') : '—' }}</td>
                            <td class="px-5 py-4 text-gray-900">{{ \App\Helpers\TimezoneHelper::formatHours((float) $record->display_worked_hours) }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $record->status)) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">No attendance records for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 px-5 py-4">{{ $records->appends(request()->query())->links() }}</div>
    </div>

    <p class="text-sm text-gray-500">If a schedule or time record is incorrect, contact HR before the payroll cutoff is validated.</p>
</div>
@endsection
