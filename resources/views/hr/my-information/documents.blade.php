@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'hr.my-information.documents'])

@section('title', 'My Documents')

@section('content')
<div class="max-w-7xl mx-auto space-y-4">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Documents</h1>
        <p class="mt-1 text-sm text-gray-500">Upload and manage your own employee documents</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md border border-green-200 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-3 rounded-md border border-red-200 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded-md border border-red-200 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($employee)
    <form method="POST" action="{{ route('hr.my-information.documents.save') }}" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- Header Identification -->
            <div class="flex items-center gap-6 mb-8 border-b border-gray-200 pb-4">
                <div class="flex items-center gap-2 flex-1 max-w-sm">
                    <label class="font-semibold text-sm text-gray-700 w-16">Name:</label>
                    <input type="text" value="{{ $employee->last_name }}, {{ $employee->first_name }}" class="flex-1 text-sm border border-gray-300 rounded px-3 py-1.5 bg-gray-100 font-medium" readonly>
                </div>
                <div class="flex items-center gap-2 w-48">
                    <label class="font-semibold text-sm text-gray-700">Empno:</label>
                    <input type="text" value="{{ $employee->employee_id }}" class="flex-1 text-sm border border-gray-300 rounded px-3 py-1.5 bg-gray-100 font-medium" readonly>
                </div>
            </div>

            <!-- Documents Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">

                <!-- Documents 1 Column -->
                <div>
                    <h3 class="font-semibold text-center text-gray-700 mb-4 border-b border-gray-200 pb-2">Documents 1</h3>
                    <div class="space-y-3">
                        @for($i = 1; $i <= 18; $i++)
                        @php $existingName = optional($existingDocuments->get('document_' . $i))->name; @endphp
                        <div class="flex items-center gap-2" x-data="{ fileName: @js($existingName ?? '') }">
                            <input type="text" x-model="fileName" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-white" placeholder="Document {{ $i }}" readonly>
                            <label class="cursor-pointer inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-gray-50 hover:bg-gray-100">
                                <i class="fas fa-file-upload mr-1"></i> Select
                                <input type="file" name="document_{{ $i }}" class="hidden" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : fileName">
                            </label>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Documents 2 Column -->
                <div>
                    <h3 class="font-semibold text-center text-gray-700 mb-4 border-b border-gray-200 pb-2">Documents 2</h3>
                    <div class="space-y-3">
                        @for($i = 19; $i <= 36; $i++)
                        @php $existingName = optional($existingDocuments->get('document_' . $i))->name; @endphp
                        <div class="flex items-center gap-2" x-data="{ fileName: @js($existingName ?? '') }">
                            <input type="text" x-model="fileName" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 bg-white" placeholder="Document {{ $i }}" readonly>
                            <label class="cursor-pointer inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-gray-50 hover:bg-gray-100">
                                <i class="fas fa-file-upload mr-1"></i> Select
                                <input type="file" name="document_{{ $i }}" class="hidden" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : fileName">
                            </label>
                        </div>
                        @endfor
                    </div>
                </div>

            </div>

            <!-- Footer / Submit -->
            <div class="mt-8 flex justify-center lg:justify-end border-t border-gray-200 pt-4">
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Upload to Database
                </button>
            </div>
        </div>
    </form>
    @else
        <!-- No employee record placeholder -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <i class="fas fa-file-alt text-6xl mb-4 text-gray-300"></i>
            <p class="text-lg">No employee record is linked to your account yet. Please contact HR/Admin.</p>
        </div>
    @endif
</div>
@endsection
