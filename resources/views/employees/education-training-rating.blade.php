@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.education-training-rating'])

@section('title', 'Education/Training/Rating')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-2">
        <div style="width:38px;height:38px;border-radius:10px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:1rem;">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Education / Training / Rating</h1>
            <p class="text-sm text-gray-500">Employee academic and development records</p>
        </div>
    </div>

    <div class="emp-card sec-personal">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-user-graduate"></i></div>
            <div>
                <h3>Employee Selection</h3>
                <p>Select employee to view and manage education details</p>
            </div>
        </div>
        <div class="emp-card-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label for="employee_name" class="form-label">Employee Name</label>
                <select id="employee_name" class="form-control">
                    <option value="">Select employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ data_get($emp, 'id') }}" data-empno="{{ data_get($emp, 'employee_id') }}">{{ data_get($emp, 'first_name') }} {{ data_get($emp, 'last_name') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="empno" class="form-label">Empno</label>
                <input type="text" id="empno" class="form-control bg-gray-50" placeholder="Auto-filled Empno" readonly>
            </div>
        </div>
        </div>
    </div>

    <div class="emp-card sec-details">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-book-open"></i></div>
            <div>
                <h3>Education Background</h3>
                <p>Academic history and qualifications</p>
            </div>
        </div>
        <div class="emp-card-body space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Highschool</label>
                <input type="text" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter Highschool">
                <div class="grid grid-cols-2 gap-2">
                    <input type="month" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Date Started">
                    <input type="month" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Date Graduated">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Graduated from</label>
                <input type="text" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter Graduated from">
                <div class="grid grid-cols-2 gap-2">
                    <input type="month" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Date Started">
                    <input type="month" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Date Graduated">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center pl-0 md:pl-6">
                <label class="text-sm font-medium text-gray-700">Course Major</label>
                <input type="text" class="md:col-span-2 w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter Course Major">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center pl-0 md:pl-6">
                <label class="text-sm font-medium text-gray-700">Course Minor</label>
                <input type="text" class="md:col-span-2 w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter Course Minor">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Post grad.</label>
                <input type="text" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter Post grad.">
                <div class="grid grid-cols-2 gap-2">
                    <input type="month" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Date Started">
                    <input type="month" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Date Graduated">
                </div>
            </div>
        </div>
    </div>

    <div class="emp-card sec-mfg">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div>
                <h3>Training / Seminar / Conferences</h3>
                <p>Development activities and certifications</p>
            </div>
        </div>
        <div class="emp-card-body">
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">Date</th>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">Name/Title</th>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">Venue</th>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">Conducted By</th>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">Cost</th>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">W/Cert</th>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">W/ Manual</th>
                            <th class="px-3 py-2 border text-left font-semibold text-gray-700">Int/Ext</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($row = 1; $row <= 6; $row++)
                            <tr>
                                <td class="p-2 border"><input type="month" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                                <td class="p-2 border"><input type="text" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                                <td class="p-2 border"><input type="text" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                                <td class="p-2 border"><input type="text" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                                <td class="p-2 border"><input type="text" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                                <td class="p-2 border"><input type="text" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                                <td class="p-2 border"><input type="text" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                                <td class="p-2 border"><input type="text" class="w-full h-9 px-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const employeeSelect = document.getElementById('employee_name');
    const empnoInput = document.getElementById('empno');

    if (!employeeSelect || !empnoInput) {
        return;
    }

    const setEmpno = () => {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        empnoInput.value = selectedOption ? (selectedOption.getAttribute('data-empno') || '') : '';
    };

    employeeSelect.addEventListener('change', setEmpno);
    setEmpno();
});
</script>
@endsection
