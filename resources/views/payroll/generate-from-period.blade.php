@extends('layouts.dashboard-base', [
    'user' => $user,
    'activeRoute' => 'payrolls.index'
])

@section('title', 'Generate Payroll from Period')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            Generate Payroll from Period
        </h1>

        <p class="text-sm text-gray-600 mb-6">
            Select a period created from Period Management.
        </p>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('payrolls.generate-from-period.store') }}">
            @csrf

            <div class="mb-5">
                <label for="period_id"
                       class="block text-sm font-medium text-gray-700 mb-2">
                    Payroll Period
                </label>

                <select name="period_id"
                        id="period_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Select Period</option>

                    @foreach($periods as $period)
                        <option value="{{ $period->id }}">
                            {{ $period->name }}
                            — {{ $period->start_date->format('M d, Y') }}
                            to {{ $period->end_date->format('M d, Y') }}
                        </option>
                    @endforeach
                </select>

                @error('period_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('payrolls.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Generate Payroll
                </button>
            </div>
        </form>
    </div>
</div>
@endsection