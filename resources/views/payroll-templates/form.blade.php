@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'payroll-templates.index'])

@section('title', isset($template) ? 'Edit Payroll Template' : 'Create Payroll Template')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ isset($template) ? 'Edit' : 'Create' }} Payroll Template</h1>
            <p class="mt-1 text-sm text-gray-600">Configure standard rates and defaults that can be assigned to positions or specific employees.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('payroll-templates.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ isset($template) ? route('payroll-templates.update', $template) : route('payroll-templates.store') }}" method="POST" class="p-6">
            @csrf
            @if(isset($template))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Template Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $template->name ?? '') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $template->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Active Template</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Earnings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Earnings</h3>
                    <p class="text-sm text-gray-500 mb-4">Leave fields blank to use system defaults or the employee's specific settings.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="monthly_rate" class="block text-sm font-medium text-gray-700 mb-1">Monthly Rate (₱)</label>
                            <input type="number" name="monthly_rate" id="monthly_rate" value="{{ old('monthly_rate', $template->monthly_rate ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="daily_rate" class="block text-sm font-medium text-gray-700 mb-1">Daily Rate (₱)</label>
                            <input type="number" name="daily_rate" id="daily_rate" value="{{ old('daily_rate', $template->daily_rate ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate (₱)</label>
                            <input type="number" name="hourly_rate" id="hourly_rate" value="{{ old('hourly_rate', $template->hourly_rate ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="overtime_rate" class="block text-sm font-medium text-gray-700 mb-1">Overtime Rate (₱/hr)</label>
                            <input type="number" name="overtime_rate" id="overtime_rate" value="{{ old('overtime_rate', $template->overtime_rate ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="night_differential_rate" class="block text-sm font-medium text-gray-700 mb-1">Night Diff. Rate (₱/hr)</label>
                            <input type="number" name="night_differential_rate" id="night_differential_rate" value="{{ old('night_differential_rate', $template->night_differential_rate ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="allowances" class="block text-sm font-medium text-gray-700 mb-1">Total Allowances (₱)</label>
                            <input type="number" name="allowances" id="allowances" value="{{ old('allowances', $template->allowances ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2 mb-4">Deductions</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="deductions" class="block text-sm font-medium text-gray-700 mb-1">Other Total Deductions (₱)</label>
                            <input type="number" name="deductions" id="deductions" value="{{ old('deductions', $template->deductions ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="sss" class="block text-sm font-medium text-gray-700 mb-1">SSS Contribution (₱)</label>
                            <input type="number" name="sss" id="sss" value="{{ old('sss', $template->sss ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="phic" class="block text-sm font-medium text-gray-700 mb-1">PhilHealth Contribution (₱)</label>
                            <input type="number" name="phic" id="phic" value="{{ old('phic', $template->phic ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="hdmf" class="block text-sm font-medium text-gray-700 mb-1">Pag-IBIG/HDMF Contribution (₱)</label>
                            <input type="number" name="hdmf" id="hdmf" value="{{ old('hdmf', $template->hdmf ?? '') }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('payroll-templates.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    {{ isset($template) ? 'Update Template' : 'Create Template' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
