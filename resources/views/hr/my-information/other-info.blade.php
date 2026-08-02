@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'hr.my-information.other-info'])

@section('title', 'Other Info')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Other Info</h1>
        <p class="mt-1 text-sm text-gray-500">Additional personal profile details</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if(!$employee)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 text-sm">
            No employee record is linked to your account yet. Please contact HR/Admin.
        </div>
    @else

    @if(isset($hasOtherInfoTable) && !$hasOtherInfoTable)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 text-sm">
            Other employee info database table is not ready. Please contact HR/Admin.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee Name</label>
                    <input type="text" value="{{ $employee->first_name }} {{ $employee->last_name }}" class="w-full h-10 px-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Empno</label>
                    <input type="text" value="{{ $employee->employee_id }}" class="w-full h-10 px-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                </div>
            </div>
        </div>

        <div class="emp-card sec-personal">
            <div class="emp-card-header">
                <div class="section-icon"><i class="fas fa-camera"></i></div>
                <div>
                    <h3>Employee Photo</h3>
                    <p>Your profile picture for identification</p>
                </div>
            </div>
            <div class="emp-card-body">
            <div class="w-52 h-52 mx-auto border border-gray-300 rounded-full bg-gray-50 overflow-hidden flex items-center justify-center mb-3">
                @if($employee->profile_photo)
                    <img src="{{ asset('storage/' . $employee->profile_photo) }}" alt="Employee Photo" class="w-full h-full object-cover">
                @else
                    <span class="text-sm text-gray-500">No photo uploaded</span>
                @endif
            </div>

            <form method="POST" action="{{ route('hr.my-information.other-info.photo') }}" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-700">
                <button type="submit" class="w-full h-10 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors" {{ isset($hasOtherInfoTable) && !$hasOtherInfoTable ? 'disabled' : '' }}>
                    Upload / Change Photo
                </button>
            </form>

            @if($employee->profile_photo)
            <form method="POST" action="{{ route('hr.my-information.other-info.photo.clear') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full h-10 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                    Clear / Remove Photo
                </button>
            </form>
            @endif
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('hr.my-information.other-info.save') }}" class="emp-card sec-details">
        <div class="emp-card-header">
            <div class="section-icon"><i class="fas fa-list-alt"></i></div>
            <div>
                <h3>Personal & Family Details</h3>
                <p>Address, physical details, and family background</p>
            </div>
        </div>
        <div class="emp-card-body space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Address</label>
                <input type="text" name="address" value="{{ old('address', data_get($otherInfo, 'address')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Pov Address</label>
                <input type="text" name="pov_address" value="{{ old('pov_address', data_get($otherInfo, 'pov_address')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">No street</label>
                <input type="text" name="no_street" value="{{ old('no_street', data_get($otherInfo, 'no_street')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Barangay</label>
                <input type="text" name="barangay" value="{{ old('barangay', data_get($otherInfo, 'barangay')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Town/District</label>
                <input type="text" name="town_district" value="{{ old('town_district', data_get($otherInfo, 'town_district')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">City/province</label>
                <input type="text" name="city_province" value="{{ old('city_province', data_get($otherInfo, 'city_province')) }}" class="form-control">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <div>
                <label class="form-label">Birthplace</label>
                <input type="text" name="birthplace" value="{{ old('birthplace', data_get($otherInfo, 'birthplace')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Religion</label>
                <input type="text" name="religion" value="{{ old('religion', data_get($otherInfo, 'religion')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Blood Type</label>
                <input type="text" name="blood_type" value="{{ old('blood_type', data_get($otherInfo, 'blood_type')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Citezenship</label>
                <input type="text" name="citizenship" value="{{ old('citizenship', data_get($otherInfo, 'citizenship')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Height</label>
                <input type="text" name="height" value="{{ old('height', data_get($otherInfo, 'height')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Weight</label>
                <input type="text" name="weight" value="{{ old('weight', data_get($otherInfo, 'weight')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', data_get($otherInfo, 'phone')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Mobile</label>
                <input type="text" name="mobile" value="{{ old('mobile', data_get($otherInfo, 'mobile')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Driver's License</label>
                <input type="text" name="drivers_license" value="{{ old('drivers_license', data_get($otherInfo, 'drivers_license')) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Prc No:</label>
                <input type="text" name="prc_no" value="{{ old('prc_no', data_get($otherInfo, 'prc_no')) }}" class="form-control">
            </div>
        </div>

        <div class="pt-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Parent's / spouse</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Father</label>
                    <input type="text" name="father" value="{{ old('father', data_get($otherInfo, 'father')) }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">Mother</label>
                    <input type="text" name="mother" value="{{ old('mother', data_get($otherInfo, 'mother')) }}" class="form-control">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Spouse</label>
                    <div class="flex items-center gap-4">
                        <input type="text" name="spouse" value="{{ old('spouse', data_get($otherInfo, 'spouse')) }}" class="form-control w-auto flex-1">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 whitespace-nowrap">
                            <input type="checkbox" name="spouse_employed" value="1" {{ old('spouse_employed', data_get($otherInfo, 'spouse_employed')) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>Spouse employed</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-6 h-10 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors" {{ isset($hasOtherInfoTable) && !$hasOtherInfoTable ? 'disabled' : '' }}>
                Save
            </button>
        </div>
        </div>
    </form>
    @endif
</div>
@endsection
