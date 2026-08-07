<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Absences Report</h3>
        <p class="mt-1 text-sm text-gray-600">Employees with absent days</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($absencesData ?? [] as $record)
                @php
                    $employee = $record->employee;
                    if (!$employee) continue;
                    $initials = strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1));
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">{{ $initials }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->department->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No absences found for the selected period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
