@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employee.schedule'])

@section('title', 'My Schedule')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Schedule</h1>
            <p class="mt-1 text-sm text-gray-600">Your assigned work schedule for {{ $monthStart->format('F Y') }}</p>
            <p class="mt-1 text-xs text-gray-500">
                {{ $employee->position?->name ?? 'No position assigned' }}
                @if($employee->department) · {{ $employee->department->name }} @endif
            </p>
        </div>

        <form method="GET" action="{{ route('employee.schedule') }}" class="flex items-end gap-2">
            <div>
                <label for="month" class="block text-xs font-medium text-gray-600">Month</label>
                <input id="month" name="month" type="month" value="{{ $month }}"
                       class="mt-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900">
            </div>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">Load</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                <div class="px-2 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">{{ $weekday }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7">
            @foreach($calendarDays as $day)
                @php
                    $schedule = $day['schedule'];
                    $dayStatus = $day['day_status'];
                @endphp
                <div class="min-h-32 border-b border-r border-gray-200 p-3 {{ !$day['in_month'] ? 'bg-gray-50' : ($day['date']->isToday() ? 'bg-blue-50' : 'bg-white') }}">
                    @if($day['in_month'])
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold {{ $day['date']->isToday() ? 'text-blue-700' : 'text-gray-700' }}">{{ $day['date']->day }}</span>
                            @if($day['date']->isToday())
                                <span class="text-[10px] font-semibold text-blue-700">TODAY</span>
                            @endif
                        </div>

                        @if($schedule)
                            <div class="mt-4 text-center">
                                <p class="text-xs font-semibold {{ match($schedule->status_color) {
                                    'green' => 'text-green-700',
                                    'yellow' => 'text-yellow-700',
                                    'red' => 'text-red-700',
                                    'blue' => 'text-blue-700',
                                    default => 'text-gray-700',
                                } }}">{{ $schedule->status_label }}</p>

                                @if($schedule->time_in && $schedule->time_out && !in_array($schedule->status, ['Day Off', 'Leave', 'Holiday', 'Regular Holiday', 'Special Holiday']))
                                    <p class="mt-1 text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($schedule->time_in)->format('g:i A') }}–{{ \Carbon\Carbon::parse($schedule->time_out)->format('g:i A') }}
                                    </p>
                                @elseif($schedule->isFlexible() && in_array($schedule->status, ['Working', 'Overtime']))
                                    <p class="mt-1 text-xs text-purple-700">Flexible · {{ \App\Helpers\TimezoneHelper::formatHours((float) $schedule->required_hours) }} required</p>
                                @endif
                            </div>
                        @endif

                        @if($dayStatus)
                            <p class="mt-3 text-center text-[11px] font-semibold {{ match($dayStatus['tone']) {
                                'green' => 'text-green-700',
                                'amber' => 'text-amber-700',
                                'red' => 'text-red-700',
                                'indigo' => 'text-indigo-700',
                                default => 'text-gray-600',
                            } }}">{{ $dayStatus['label'] }}</p>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <p class="text-sm text-gray-500">This page is view only. Blank dates have no assigned schedule. Contact HR if an assignment needs correction.</p>
</div>
@endsection
