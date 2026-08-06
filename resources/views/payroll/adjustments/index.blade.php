@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'payroll-adjustments.index'])
@section('title', 'Payroll Adjustments')
@section('content')
<div class="min-h-screen bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <<div class="mb-6 flex justify-end">
    <a href="{{ route('payroll-adjustments.create') }}"
       class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>
        Add Adjustment
    </a>
    </div>

    
    @if(session('success'))<div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    <form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 mb-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <select name="employee_id" class="rounded-lg border-gray-300"><option value="">All employees</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected(request('employee_id')===$employee->id)>{{ $employee->employee_id }} — {{ $employee->full_name }}</option>@endforeach</select>
        <select name="category" class="rounded-lg border-gray-300"><option value="">All categories</option>@foreach(['bonus'=>'Bonus','allowance'=>'Allowance','deduction'=>'Deduction','manual'=>'Manual Adjustment'] as $value=>$label)<option value="{{ $value }}" @selected(request('category')===$value)>{{ $label }}</option>@endforeach</select>
        <select name="status" class="rounded-lg border-gray-300"><option value="">All statuses</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select>
        <button class="rounded-lg bg-gray-300 text-black px-4 py-2">Filter</button>
    </form>
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr>@foreach(['Employee','Adjustment','Type','Amount','Effective','Status','Actions'] as $h)<th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $h }}</th>@endforeach</tr></thead>
        <tbody class="divide-y divide-gray-200">@forelse($adjustments as $a)<tr>
            <td class="px-5 py-4"><div class="text-sm font-medium text-gray-900">{{ $a->employee?->full_name }}</div><div class="text-xs text-gray-500">{{ $a->employee?->employee_id }} · {{ $a->employee?->department?->name ?? 'N/A' }}</div></td>
            <td class="px-5 py-4"><div class="text-sm font-medium">{{ $a->name }}</div><div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($a->reason, 70) }}</div></td>
            <td class="px-5 py-4 text-sm"><span class="capitalize">{{ str_replace('_',' ',$a->category) }}</span><div class="text-xs text-gray-500 capitalize">{{ str_replace('_',' ',$a->frequency) }} · {{ $a->direction }}</div></td>
            <td class="px-5 py-4 text-sm font-semibold {{ $a->direction === 'earning' ? 'text-green-700' : 'text-red-700' }}">{{ $a->direction === 'earning' ? '+' : '-' }}₱{{ number_format((float)$a->amount,2) }}</td>
            <td class="px-5 py-4 text-sm">{{ $a->effective_from->format('M d, Y') }}@if($a->frequency==='recurring')<div class="text-xs text-gray-500">to {{ $a->effective_to?->format('M d, Y') ?? 'No end date' }}</div>@endif</td>
            <td class="px-5 py-4"><span class="px-2 py-1 rounded-full text-xs {{ $a->is_active ? 'bg-green-100 text-green-700':'bg-gray-100 text-gray-600' }}">{{ $a->is_active ? 'Active':'Inactive' }}</span></td>
            <td class="px-5 py-4 whitespace-nowrap"><a href="{{ route('payroll-adjustments.edit',$a) }}" class="text-blue-600 mr-3"><i class="fas fa-edit"></i></a><form class="inline" method="POST" action="{{ route('payroll-adjustments.destroy',$a) }}" onsubmit="return confirm('Delete this adjustment?')">@csrf @method('DELETE')<button class="text-red-600"><i class="fas fa-trash"></i></button></form></td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-12 text-center text-gray-500">No payroll adjustments found.</td></tr>@endforelse</tbody></table>
    </div>
    <div class="mt-4">{{ $adjustments->links() }}</div>
</div></div>
@endsection
