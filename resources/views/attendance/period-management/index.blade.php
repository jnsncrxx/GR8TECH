@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.period-management.index'])

@section('title', 'Payroll Period Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Payroll Period Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Set payroll dates, monitor each cutoff, and control when payroll can be processed and locked.</p>
                </div>
                @if($user->role !== 'employee')
                    <a href="{{ route('attendance.period-management.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Create Payroll Period
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @foreach(['success' => 'green', 'warning' => 'yellow', 'error' => 'red', 'info' => 'blue'] as $key => $color)
            @if(session($key))
                <div class="mb-5 rounded-lg border border-{{ $color }}-200 bg-{{ $color }}-50 p-4 text-sm text-{{ $color }}-800">
                    {{ session($key) }}
                </div>
            @endif
        @endforeach



        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Periods</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $periods->count() }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Open / Validation</p>
                <p class="mt-1 text-2xl font-bold text-blue-700">{{ $periods->whereIn('status', ['open', 'for_validation', 'ready'])->count() }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">In Payroll Process</p>
                <p class="mt-1 text-2xl font-bold text-purple-700">{{ $periods->whereIn('status', ['processing', 'for_review', 'finalized'])->count() }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Locked</p>
                <p class="mt-1 text-2xl font-bold text-gray-700">{{ $periods->where('status', 'locked')->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
            <div class="flex flex-col gap-3 md:flex-row">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Search by period, company, dates, or status..."
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Period::STATUSES as $status)
                        <option value="{{ $status }}">{{ \App\Models\Period::labelForStatus($status) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($periods->isEmpty())
            <div class="bg-white rounded-lg border border-gray-200 py-16 text-center">
                <i class="fas fa-calendar-alt text-5xl text-gray-300"></i>
                <h2 class="mt-4 text-lg font-semibold text-gray-900">No payroll periods yet</h2>
                <p class="mt-2 text-sm text-gray-500">Create the first official cutoff period to begin payroll validation.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payroll Period</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coverage</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay Date</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="periodsTableBody" class="divide-y divide-gray-200">
                            @foreach($periods as $period)
                                @php
                                    $statusClasses = match($period->status) {
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'open' => 'bg-blue-100 text-blue-800',
                                        'for_validation' => 'bg-yellow-100 text-yellow-800',
                                        'ready' => 'bg-green-100 text-green-800',
                                        'processing' => 'bg-purple-100 text-purple-800',
                                        'for_review' => 'bg-indigo-100 text-indigo-800',
                                        'finalized' => 'bg-emerald-100 text-emerald-800',
                                        'locked' => 'bg-slate-200 text-slate-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                    $nextStatus = in_array($period->status, ['draft', 'open'], true) ? $period->nextStatus() : null;
                                @endphp
                                <tr class="period-row hover:bg-gray-50"
                                    data-status="{{ $period->status }}"
                                    data-search="{{ strtolower($period->name . ' ' . ($period->company->name ?? '') . ' ' . $period->status_label . ' ' . $period->start_date->format('Y-m-d') . ' ' . $period->end_date->format('Y-m-d')) }}">
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-900">{{ $period->name }}</div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $period->company->name ?? 'Unassigned Company' }}
                                            @if($period->previousPeriod)
                                                · Previous: {{ $period->previousPeriod->name }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 whitespace-nowrap">
                                        <div>{{ $period->start_date->format('M j, Y') }} – {{ $period->end_date->format('M j, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $period->duration }} calendar days · {{ $period->working_days ?? 0 }} weekdays</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $period->payroll_date?->format('M j, Y') ?? 'Not set' }}
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        <div>{{ str($period->period_type ?? 'regular')->replace('_', ' ')->title() }}</div>
                                        <div class="text-xs text-gray-500">{{ str($period->processing_type ?? 'regular')->replace('_', ' ')->title() }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            @if($period->status === 'locked')<i class="fas fa-lock mr-1"></i>@endif
                                            {{ $period->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('attendance.period-management.show', $period->id) }}"
                                               class="px-3 py-2 rounded-lg bg-green-50 text-green-700 text-sm font-medium hover:bg-green-100">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>

                                            @if($user->role !== 'employee' && $nextStatus)
                                                <form method="POST"
                                                      action="{{ route('attendance.period-management.status', $period->id) }}"
                                                      class="inline period-status-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $nextStatus }}">

                                                    <button type="button"
                                                            class="open-status-modal px-3 py-2 rounded-lg bg-blue-50 text-blue-700 text-sm font-medium hover:bg-blue-100 transition-colors"
                                                            data-period-name="{{ $period->name }}"
                                                            data-status-label="{{ \App\Models\Period::labelForStatus($nextStatus) }}">
                                                        {{ \App\Models\Period::labelForStatus($nextStatus) }}
                                                    </button>
                                                </form>
                                            @endif

                                            @if($user->role !== 'employee' && $period->canBeDeleted())
                                                <form method="POST" action="{{ route('attendance.period-management.destroy', $period->id) }}" class="inline"
                                                      onsubmit="return confirm('Delete this draft payroll period?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-2 rounded-lg bg-red-50 text-red-700 text-sm font-medium hover:bg-red-100">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="noResults" class="hidden py-10 text-center text-sm text-gray-500">No matching payroll periods.</div>
            </div>
        @endif
    </div>
</div>

<!-- Status Confirmation Modal -->
<div id="statusConfirmationModal"
     class="fixed inset-0 z-50 hidden items-center justify-center px-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="statusModalTitle">

    <div id="statusModalBackdrop"
         class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

    <div id="statusModalPanel"
         class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
                    <i class="fas fa-arrow-right text-blue-700"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <h3 id="statusModalTitle" class="text-lg font-semibold text-gray-900">
                        Update Payroll Period
                    </h3>

                    <p id="statusModalMessage" class="mt-2 text-sm leading-6 text-gray-600">
                        Are you sure you want to update this payroll period?
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
            <button type="button"
                    id="cancelStatusUpdate"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                Cancel
            </button>

            <button type="button"
                    id="confirmStatusUpdate"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fas fa-check mr-2"></i>
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');

function filterPeriods() {
    const search = searchInput.value.toLowerCase().trim();
    const status = statusFilter.value;
    const rows = [...document.querySelectorAll('.period-row')];
    let visible = 0;

    rows.forEach(row => {
        const matchesSearch = !search || row.dataset.search.includes(search);
        const matchesStatus = !status || row.dataset.status === status;
        const show = matchesSearch && matchesStatus;
        row.classList.toggle('hidden', !show);
        if (show) visible++;
    });

    document.getElementById('noResults')?.classList.toggle('hidden', visible !== 0);
}

searchInput?.addEventListener('input', filterPeriods);
statusFilter?.addEventListener('change', filterPeriods);

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('statusConfirmationModal');
    const backdrop = document.getElementById('statusModalBackdrop');
    const panel = document.getElementById('statusModalPanel');
    const message = document.getElementById('statusModalMessage');
    const cancelButton = document.getElementById('cancelStatusUpdate');
    const confirmButton = document.getElementById('confirmStatusUpdate');

    let selectedForm = null;
    let lastFocusedButton = null;

    function openModal(form, periodName, statusLabel, triggerButton) {
        selectedForm = form;
        lastFocusedButton = triggerButton;

        message.textContent = `Move "${periodName}" to ${statusLabel}?`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            cancelButton.focus();
        });
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');

        selectedForm = null;

        if (lastFocusedButton) {
            lastFocusedButton.focus();
        }

        lastFocusedButton = null;
    }

    document.querySelectorAll('.open-status-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = button.closest('.period-status-form');

            openModal(
                form,
                button.dataset.periodName || 'this payroll period',
                button.dataset.statusLabel || 'the next status',
                button
            );
        });
    });

    confirmButton?.addEventListener('click', function () {
        if (!selectedForm) {
            return;
        }

        confirmButton.disabled = true;
        cancelButton.disabled = true;

        confirmButton.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Updating...
        `;

        selectedForm.submit();
    });

    cancelButton?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);

    panel?.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
@endsection
