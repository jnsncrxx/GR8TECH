@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'hr.my-information.ytd-info'])

@section('title', 'YTD - INFO')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">YTD - INFO</h1>
        <p class="mt-1 text-sm text-gray-500">Your year-to-date information</p>
    </div>

    @if(!$employee)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 text-sm">
            No employee record is linked to your account yet. Please contact HR/Admin.
        </div>
    @else

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee Name</label>
                <input type="text" value="{{ $employee->first_name }} {{ $employee->last_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Empno</label>
                <input type="text" value="{{ $employee->employee_id }}" class="w-full h-10 px-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
            </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Previous Companies</h2>

            <div class="grid grid-cols-1 gap-4">
                @php
                    $fields = [
                        'TAXINC',
                        'TAX',
                        '13MON',
                        '13MON TAX',
                        'SSS EMPLYE',
                        'SSS EMPLYR',
                        'MED EMPLYE',
                        'MED EMPLYR',
                        'PAG EMPLYE',
                        'PAG EMPLYR',
                        'ECC',
                    ];
                @endphp

                @foreach($fields as $field)
                    <div class="flex items-center gap-2">
                        <label class="w-36 shrink-0 text-sm font-medium text-gray-700 whitespace-nowrap text-right">{{ $field }}</label>
                        <span class="text-gray-500 shrink-0">:</span>
                        <input type="text" class="w-full max-w-xl h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter {{ $field }}">
                    </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-500 mt-4">
                YTD figures are not yet stored in the database on this system, so this section mirrors the admin
                layout but isn't wired up to save data yet.
            </p>
        </div>
    </div>
    @endif
</div>
@endsection
