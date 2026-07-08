@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.official-business'])

@section('title', 'Official Business')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Official Business</h1>
            @if($isReviewer)
                <p class="mt-1 text-sm text-gray-600">Review and manage employee OB requests</p>
            @else
                <p class="mt-1 text-sm text-gray-600">Submit and track your Official Business requests</p>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            @unless($isReviewer)
            <button id="applyObBtn" onclick="openObModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Apply for Official Business
            </button>
            @endunless
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-lg bg-green-50 text-green-700 border border-green-200 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Official Business Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Rejected</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary['rejected'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($isReviewer)
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="GET" action="{{ route('attendance.official-business') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select id="employee" name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-white text-gray-900" style="background-color: white !important; color: #111827 !important;">
                    <option value="" style="color: #111827 !important;">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }} style="color: #111827 !important;">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="department" name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-white text-gray-900" style="background-color: white !important; color: #111827 !important;">
                    <option value="" style="color: #111827 !important;">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }} style="color: #111827 !important;">
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors bg-white text-gray-900" style="background-color: white !important; color: #111827 !important;">
                    <option value="" style="color: #111827 !important;">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} style="color: #111827 !important;">Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }} style="color: #111827 !important;">Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }} style="color: #111827 !important;">Rejected</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="w-full px-10 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Apply
                </button>
                <a href="{{ route('attendance.official-business') }}" class="w-full px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>
    @endif

    <!-- Official Business Records -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Official Business Records</h3>
            <p class="mt-1 text-sm text-gray-600">Employee OB requests and approvals</p>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if($isReviewer)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reason
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reviewed By
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($obRequests as $ob)
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                            $obStatus = $ob->isPending() ? 'pending' : ($ob->isApproved() ? 'approved' : 'rejected');
                            $statusColor = $statusColors[$obStatus];
                            $initials = strtoupper(substr($ob->employee->first_name ?? '', 0, 1) . substr($ob->employee->last_name ?? '', 0, 1));
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            @if($isReviewer)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $ob->employee->first_name ?? '' }} {{ $ob->employee->last_name ?? '' }}</div>
                                        <div class="text-sm text-gray-500">{{ $ob->employee->department->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $ob->date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $ob->reason }}">{{ \Illuminate\Support\Str::limit($ob->reason, 30) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                    <div class="w-1.5 h-1.5 rounded-full mr-1.5 {{ str_replace('text-', 'bg-', $statusColor) }}"></div>
                                    {{ ucfirst($obStatus) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $ob->reviewer->full_name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($ob->isPending())
                                        @if($isReviewer)
                                            <button onclick="approveOb('{{ $ob->id }}')" class="text-green-600 hover:text-green-900 transition-colors" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="rejectOb('{{ $ob->id }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('attendance.official-business.cancel', $ob->id) }}" onsubmit="return confirm('Cancel this OB request?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-500 hover:text-gray-900 transition-colors" title="Cancel">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isReviewer ? 6 : 5 }}" class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <i class="fas fa-briefcase text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500 text-lg font-medium mb-2">No official business requests found</p>
                                    <p class="text-gray-400 text-sm">Try adjusting your filters or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($obRequests->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $obRequests->firstItem() }} to {{ $obRequests->lastItem() }} of {{ $obRequests->total() }} results
                </div>
                <div class="flex items-center space-x-2">
                    @if($obRequests->onFirstPage())
                        <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded">Previous</span>
                    @else
                        <a href="{{ $obRequests->previousPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif

                    @for($i = 1; $i <= $obRequests->lastPage(); $i++)
                        @if($i == $obRequests->currentPage())
                            <span class="px-3 py-2 text-sm text-white bg-blue-600 rounded">{{ $i }}</span>
                        @else
                            <a href="{{ $obRequests->url($i) }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($obRequests->hasMorePages())
                        <a href="{{ $obRequests->nextPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded">Next</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            <div class="p-4 space-y-4">
                @forelse($obRequests as $ob)
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                        $obStatus = $ob->isPending() ? 'pending' : ($ob->isApproved() ? 'approved' : 'rejected');
                        $statusColor = $statusColors[$obStatus];
                        $initials = strtoupper(substr($ob->employee->first_name ?? '', 0, 1) . substr($ob->employee->last_name ?? '', 0, 1));
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $ob->employee->first_name ?? '' }} {{ $ob->employee->last_name ?? '' }}</div>
                                    <div class="text-sm text-gray-500">{{ $ob->employee->department->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                <div class="w-1.5 h-1.5 rounded-full mr-1 {{ str_replace('text-', 'bg-', $statusColor) }}"></div>
                                {{ ucfirst($obStatus) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                            <div>
                                <div class="text-gray-500">Date</div>
                                <div class="font-medium">{{ $ob->date->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Reviewed By</div>
                                <div class="font-medium">{{ $ob->reviewer->full_name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="text-sm mb-3">
                            <div class="text-gray-500">Reason</div>
                            <div class="font-medium">{{ \Illuminate\Support\Str::limit($ob->reason, 50) }}</div>
                        </div>
                        @if($ob->isPending())
                        <div class="flex justify-end space-x-2">
                            @if($isReviewer)
                                <button onclick="approveOb('{{ $ob->id }}')" class="text-green-600 hover:text-green-900 transition-colors">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                                <button onclick="rejectOb('{{ $ob->id }}')" class="text-red-600 hover:text-red-900 transition-colors">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>
                            @else
                                <form method="POST" action="{{ route('attendance.official-business.cancel', $ob->id) }}" onsubmit="return confirm('Cancel this OB request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-gray-900 transition-colors">
                                        <i class="fas fa-ban mr-1"></i>Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-briefcase text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg font-medium mb-2">No official business requests found</p>
                            <p class="text-gray-400 text-sm">Try adjusting your filters or date range.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- OB Application Modal -->
@unless($isReviewer)
<div id="obModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeObModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Request Official Business</h3>
                <button onclick="closeObModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="obForm" method="POST" action="{{ route('attendance.official-business.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="obDate" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" id="obDate" name="date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           min="{{ now()->toDateString() }}" value="{{ old('date') }}">
                </div>

                <div>
                    <label for="obReason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea id="obReason" name="reason" rows="3" required maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                              placeholder="Please provide a reason for your Official Business request...">{{ old('reason') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeObModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endunless

<!-- Reviewer: Reject Modal -->
@if($isReviewer)
<div id="obRejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999;" onclick="closeRejectModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" style="max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject OB Request</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="obRejectForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                <div>
                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection</label>
                    <textarea id="rejectionReason" name="rejection_reason" rows="3" required maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-lg text-white hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function openObModal() {
    const modal = document.getElementById('obModal');
    if (!modal) return;
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    const form = document.getElementById('obForm');
    if (form) form.reset();
    const dateInput = document.getElementById('obDate');
    if (dateInput) dateInput.min = new Date().toISOString().split('T')[0];
}

function closeObModal() {
    const modal = document.getElementById('obModal');
    if (modal) modal.style.display = 'none';
}

@if($isReviewer)
function approveOb(requestId) {
    if (!confirm('Are you sure you want to approve this Official Business request?')) {
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ url('attendance/official-business') }}/${requestId}/status`;
    form.innerHTML = `
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="approved">
    `;
    document.body.appendChild(form);
    form.submit();
}

function rejectOb(requestId) {
    const modal = document.getElementById('obRejectModal');
    const form = document.getElementById('obRejectForm');
    if (!modal || !form) return;
    form.action = `{{ url('attendance/official-business') }}/${requestId}/status`;
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeRejectModal() {
    const modal = document.getElementById('obRejectModal');
    if (modal) modal.style.display = 'none';
}
@endif
</script>
@endsection