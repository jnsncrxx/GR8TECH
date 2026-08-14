<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Employee Filings</h3>
        <p class="mt-1 text-sm text-gray-600">All submitted filings (Leave, OT, OB, Attendance Corrections)</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filing Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($filingsData ?? [] as $filing)
                @php
                    $employee = $filing->employee;
                    if (!$employee) continue;
                    $initials = strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1));
                    
                    $statusColor = 'bg-gray-50 text-gray-700 border border-gray-200';
                    if ($filing->status === 'approved') $statusColor = 'bg-green-50 text-green-700 border border-green-200';
                    elseif ($filing->status === 'pending') $statusColor = 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                    elseif ($filing->status === 'rejected') $statusColor = 'bg-red-50 text-red-700 border border-red-200';
                    elseif ($filing->status === 'applied') $statusColor = 'bg-blue-50 text-blue-700 border border-blue-200';
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">{{ $initials }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $filing->type }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($filing->date)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $statusColor }}">
                            {{ ucfirst($filing->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No filings found for the selected period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
