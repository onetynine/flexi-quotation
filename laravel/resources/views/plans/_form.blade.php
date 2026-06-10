<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" required
                   value="{{ old('name', $plan->name ?? '') }}"
                   placeholder="e.g. LAPTOP - CI7-8 / 16GB RAM / 256GB SSD"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Specs</label>
            <input type="text" name="specs"
                   value="{{ old('specs', $plan->specs ?? '') }}"
                   placeholder="e.g. HP EliteBook, Intel Core i7, 16GB RAM"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deposit / Unit (RM) <span class="text-red-500">*</span></label>
            <input type="number" name="deposit_per_unit" step="0.01" min="0" required
                   value="{{ old('deposit_per_unit', $plan->deposit_per_unit ?? 0) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Daily Rate (RM) <span class="text-red-500">*</span></label>
            <input type="number" name="daily_rate" step="0.01" min="0" required
                   value="{{ old('daily_rate', $plan->daily_rate ?? 0) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Weekly Rate (RM)</label>
            <input type="number" name="weekly_rate" step="0.01" min="0"
                   value="{{ old('weekly_rate', $plan->weekly_rate ?? 0) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Rate (RM)</label>
            <input type="number" name="monthly_rate" step="0.01" min="0"
                   value="{{ old('monthly_rate', $plan->monthly_rate ?? 0) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
        </div>
        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="hidden" name="is_custom" value="0">
                <input type="checkbox" name="is_custom" value="1" {{ old('is_custom', $plan->is_custom ?? false) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-yellow-400 focus:ring-yellow-400">
                Custom plan (manual rate entry)
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" {{ old('active', $plan->active ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-yellow-400 focus:ring-yellow-400">
                Active
            </label>
        </div>
    </div>
</div>
