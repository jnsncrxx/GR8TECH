@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'payroll.runs'])

@section('title', 'Payroll Runs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Payroll Runs</h1>
            <p class="mt-1 text-sm text-gray-600">Preview, generate, review, finalize, lock, and export payroll from validated cutoff periods.</p>
        </div>
        <a href="{{ route('attendance.period-management.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
            <i class="fas fa-calendar-week mr-2"></i>Prepare Cutoff Period
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('payroll.runs') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
            <div class="w-full sm:w-64">
                <label for="run-status" class="block text-sm font-medium text-gray-700 mb-1">Workflow status</label>
                <select id="run-status" name="status" class="w-full border-gray-300 rounded-lg">
                    <option value="">All statuses</option>
                    <option value="ready" @selected(request('status') === 'ready')>Ready for Payroll</option>
                    <option value="processing" @selected(request('status') === 'processing')>Processing</option>
                    <option value="for_review" @selected(request('status') === 'for_review')>For Review</option>
                    <option value="finalized" @selected(request('status') === 'finalized')>Finalized</option>
                    <option value="locked" @selected(request('status') === 'locked')>Locked</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">Apply</button>
            @if(request()->filled('status'))
                <a href="{{ route('payroll.runs') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($payrollPeriods as $workflowPeriod)
                <div @class([
                    'flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 sm:px-6 py-5 hover:bg-gray-50 transition-colors',
                    'bg-green-50 ring-2 ring-inset ring-green-300' => request('period_id') === (string) $workflowPeriod->id,
                ])>
                    <a href="{{ $workflowPeriod->status === \App\Models\Period::STATUS_READY ? route('payroll.periods.preview', $workflowPeriod->id) : route('payroll.periods.review', $workflowPeriod->id) }}" class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900">{{ $workflowPeriod->name }}</div>
                        <div class="mt-1 text-sm text-gray-500">
                            {{ optional($workflowPeriod->start_date)->format('M j, Y') ?? 'No start date' }} -
                            {{ optional($workflowPeriod->end_date)->format('M j, Y') ?? 'No end date' }}
                            @if($workflowPeriod->department) · {{ $workflowPeriod->department->name }} @endif
                        </div>
                    </a>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">{{ $workflowPeriod->payrolls_count }} employee(s)</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            {{ in_array($workflowPeriod->status, [\App\Models\Period::STATUS_FINALIZED, \App\Models\Period::STATUS_LOCKED], true) ? 'bg-green-100 text-green-800' : ($workflowPeriod->status === \App\Models\Period::STATUS_FOR_REVIEW ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $workflowPeriod->status_label }}
                        </span>
                        @if($workflowPeriod->status === \App\Models\Period::STATUS_READY)
                            <a href="{{ route('payroll.periods.preview', $workflowPeriod->id) }}"
                               class="inline-flex items-center px-3 py-2 rounded-lg border border-green-700 bg-green-600 text-white text-sm font-semibold hover:bg-green-700">
                                <i class="fas fa-eye mr-2"></i>Preview Payroll
                            </a>
                        @elseif($workflowPeriod->status === \App\Models\Period::STATUS_FINALIZED)
                            <form id="lock-payroll-form-{{ $workflowPeriod->id }}" method="POST" action="{{ route('payroll.periods.lock', $workflowPeriod->id) }}">
                                @csrf
                                <button type="button"
                                        onclick="openPayrollLockModal('lock-payroll-form-{{ $workflowPeriod->id }}', @js($workflowPeriod->name))"
                                        class="inline-flex items-center px-3 py-2 rounded-lg border-2 border-amber-800 bg-amber-400 text-black text-sm font-bold shadow-sm hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2">
                                    <i class="fas fa-lock mr-2"></i>Lock Payroll
                                </button>
                            </form>
                        @else
                            <a href="{{ route('payroll.periods.review', $workflowPeriod->id) }}" class="p-2 text-gray-400 hover:text-blue-600" aria-label="Open payroll run">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-layer-group text-3xl text-gray-300"></i>
                    <p class="mt-3 text-sm text-gray-600">No generated payroll runs match this filter.</p>
                </div>
            @endforelse
        </div>
        @if($payrollPeriods->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $payrollPeriods->links() }}</div>
        @endif
    </div>
</div>

@include('components.payroll-lock-modal')
@endsection
