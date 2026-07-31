<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
    <input type="text" name="name" value="{{ old('name', $loanType->name ?? '') }}" placeholder="e.g. Salary Loan"
           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $loanType->description ?? '') }}</textarea>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Default Interest Rate (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="default_interest_rate"
               value="{{ old('default_interest_rate', $loanType->default_interest_rate ?? 0) }}"
               class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Interest Type</label>
        <select name="interest_type" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            <option value="flat" {{ old('interest_type', $loanType->interest_type ?? 'flat') === 'flat' ? 'selected' : '' }}>Flat</option>
            <option value="diminishing" {{ old('interest_type', $loanType->interest_type ?? '') === 'diminishing' ? 'selected' : '' }}>Diminishing Balance</option>
        </select>
    </div>
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" id="is_active" value="1"
           {{ old('is_active', $loanType->is_active ?? true) ? 'checked' : '' }}
           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
    <label for="is_active" class="text-sm text-gray-700">Active (available for new loan requests)</label>
</div>