<div class="space-y-6">

    {{-- Customer Info --}}
    <div class="bg-white rounded-lg shadow p-6" x-data="customerPicker({{ $customers->toJson() }})">
        <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Customer Information</h2>

        {{-- Customer selector row --}}
        <div class="flex gap-2 mb-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Existing Customer</label>
                <select x-model="selectedId" @change="fillCustomer()"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    <option value="">— Type manually or select —</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}"
                            {{ old('customer_id', $quotation->customer_id ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}{{ $c->contact_number ? ' · '.$c->contact_number : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" @click="showModal = true"
                        class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-3 py-2 rounded transition text-sm whitespace-nowrap">
                    + New
                </button>
            </div>
        </div>
        <input type="hidden" name="customer_id" :value="selectedId">

        {{-- Customer fields (auto-filled or manual) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                <input type="text" name="customer_name" required x-model="form.name"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" x-model="form.email"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                <input type="text" name="contact_number" x-model="form.contact_number"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                <input type="text" name="delivery_address" x-model="form.delivery_address"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
        </div>

        {{-- New Customer Modal --}}
        <div x-show="showModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
             @keydown.escape.window="showModal = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-800">New Customer</h3>
                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>
                <div class="space-y-3" id="modal-fields">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="modal.name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" x-model="modal.email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" x-model="modal.contact_number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                        <input type="text" x-model="modal.delivery_address" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    </div>
                </div>
                <div x-show="modalError" x-text="modalError" class="mt-2 text-sm text-red-600"></div>
                <div class="mt-5 flex gap-3">
                    <button type="button" @click="saveCustomer()"
                            :disabled="saving"
                            class="bg-yellow-400 hover:bg-yellow-500 disabled:opacity-50 text-gray-900 font-bold px-5 py-2 rounded transition text-sm">
                        <span x-show="!saving">Save Customer</span>
                        <span x-show="saving">Saving…</span>
                    </button>
                    <button type="button" @click="showModal = false"
                            class="bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded hover:bg-gray-50 transition text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Plan Details --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Plan Details</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Plan <span class="text-red-500">*</span></label>
                <select name="plan_id" x-model="planId" @change="selectPlan()"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    <option value="">-- Select a plan --</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}"
                            {{ old('plan_id', $quotation->plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                            @if(!$plan->is_custom) (RM {{ number_format($plan->daily_rate, 0) }}/day | Deposit RM {{ number_format($plan->deposit_per_unit, 0) }}/unit) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div x-show="isCustomPlan" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">Custom Plan Name <span class="text-red-500">*</span></label>
                <input type="text" x-model="planName"
                       placeholder="e.g. Branded Tier-1 Custom A13526"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Device Specs</label>
                <input type="text" x-model="planSpecs" :readonly="!isCustomPlan"
                       :class="!isCustomPlan ? 'bg-gray-50 text-gray-600' : ''"
                       placeholder="e.g. HP 840 G3, Intel Core i7, 32GB RAM, 512GB SSD"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>

            <input type="hidden" name="plan_name" :value="planName">
            <input type="hidden" name="plan_specs" :value="planSpecs">
            <input type="hidden" name="is_custom_plan" :value="isCustomPlan ? '1' : '0'">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" min="1" x-model.number="quantity"
                           @input="updateDeposit(); calculate()"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rate / Day (RM) <span class="text-red-500">*</span></label>
                    <input type="number" name="rate_per_day" step="0.01" min="0"
                           x-model.number="ratePerDay" @input="calculate()"
                           :readonly="!isCustomPlan" :class="!isCustomPlan ? 'bg-gray-50 text-gray-600' : ''"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                </div>
            </div>
        </div>
    </div>

    {{-- Rental Duration --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Rental Duration</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" x-model="startDate"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
                <input type="date" name="end_date" x-model="endDate"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Days</label>
                <input type="text" readonly :value="totalDays"
                       class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-700 font-semibold">
                <input type="hidden" name="total_days" :value="totalDays">
            </div>
        </div>
    </div>

    {{-- Delivery & Deposit --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Delivery & Deposit</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Option</label>
                <input type="text" name="delivery_option"
                       value="{{ old('delivery_option', $quotation->delivery_option ?? 'Self Collect') }}"
                       placeholder="e.g. Self Collect / Delivery"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Fee (RM)</label>
                <input type="number" name="delivery_fee" step="0.01" min="0"
                       x-model.number="deliveryFee" @input="calculate()"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Option</label>
                <select name="deposit_option" x-model="depositOption" @change="updateDeposit()"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    <option value="standard">Standard (per unit from plan)</option>
                    <option value="custom">Custom Amount</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deposit Amount (RM)
                    <span class="text-xs text-gray-400" x-show="depositOption === 'standard' && standardDepositPerUnit > 0" x-cloak>
                        — RM <span x-text="standardDepositPerUnit"></span>/unit × <span x-text="quantity"></span> units
                    </span>
                </label>
                <input type="number" name="deposit_amount" step="0.01" min="0"
                       x-model.number="depositAmount" @input="calculate()"
                       :readonly="depositOption === 'standard' && standardDepositPerUnit > 0"
                       :class="(depositOption === 'standard' && standardDepositPerUnit > 0) ? 'bg-gray-50 text-gray-600' : ''"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Financial Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tax %</label>
                <input type="number" name="tax_percent" step="0.01" min="0" max="100"
                       x-model.number="taxPercent" @input="calculate()"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 text-sm space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Rental Fee (<span x-text="ratePerDay"></span>/day × <span x-text="quantity"></span> units × <span x-text="totalDays"></span> days)</span>
                <span class="font-medium" x-text="fmt(rentalFee)"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Delivery</span>
                <span class="font-medium" x-text="fmt(deliveryFee)"></span>
            </div>
            <div class="flex justify-between border-t border-gray-300 pt-2">
                <span class="text-gray-500 text-xs">Taxable Subtotal</span>
                <span class="font-medium text-xs" x-text="fmt(subtotal)"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Tax (<span x-text="taxPercent"></span>%)</span>
                <span class="font-medium" x-text="fmt(taxAmount)"></span>
            </div>
            <div class="flex justify-between border-t border-dashed border-gray-300 pt-2">
                <span class="text-gray-600">Deposit <span class="text-xs text-green-600 font-medium">(No SST)</span></span>
                <span class="font-medium" x-text="fmt(depositAmount)"></span>
            </div>
            <div class="flex justify-between border-t-2 border-gray-400 pt-2">
                <span class="font-bold text-gray-800">Total Payable</span>
                <span class="font-bold text-yellow-600 text-base" x-text="fmt(totalPayable)"></span>
            </div>
        </div>

        <input type="hidden" name="rental_fee" :value="rentalFee">
        <input type="hidden" name="tax_amount" :value="taxAmount">
        <input type="hidden" name="subtotal" :value="subtotal">
        <input type="hidden" name="total_payable" :value="totalPayable">
    </div>

    {{-- Agent Info --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Agent Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agent Name</label>
                <input type="text" name="agent_name"
                       value="{{ old('agent_name', $quotation->agent_name ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agent Contact</label>
                <input type="text" name="agent_contact"
                       value="{{ old('agent_contact', $quotation->agent_contact ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agent Email</label>
                <input type="email" name="agent_email"
                       value="{{ old('agent_email', $quotation->agent_email ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
        </div>
    </div>

    {{-- Meta --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Quotation Meta</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
                    @foreach(['Pending', 'Accepted', 'Cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $quotation->status ?? 'Pending') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quotation Link</label>
                <input type="text" name="quotation_link"
                       value="{{ old('quotation_link', $quotation->quotation_link ?? '') }}"
                       placeholder="https://..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>
        </div>
    </div>

</div>
