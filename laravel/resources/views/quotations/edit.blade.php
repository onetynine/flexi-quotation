@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('quotations.index') }}" class="hover:text-gray-800">Quotations</a>
        <span>/</span>
        <a href="{{ route('quotations.show', $quotation) }}" class="hover:text-gray-800">{{ $quotation->quotation_no }}</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Edit</span>
    </div>

    <form method="POST" action="{{ route('quotations.update', $quotation) }}"
          x-data="quotationForm({{ $plans->toJson() }}, {{ $quotation->toJson() }})"
          x-init="init()"
          @submit.prevent="$el.submit()">
        @csrf
        @method('PUT')
        @include('quotations._form')
        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-6 py-2 rounded transition">
                Save Changes
            </button>
            <a href="{{ route('quotations.show', $quotation) }}"
               class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function customerPicker(customers) {
    return {
        customers,
        selectedId: '',
        showModal: false,
        saving: false,
        modalError: '',
        form: { name: '', email: '', contact_number: '', delivery_address: '' },
        modal: { name: '', email: '', contact_number: '', delivery_address: '' },

        fillCustomer() {
            const c = this.customers.find(c => c.id == this.selectedId);
            if (c) {
                this.form.name             = c.name;
                this.form.email            = c.email || '';
                this.form.contact_number   = c.contact_number || '';
                this.form.delivery_address = c.delivery_address || '';
            }
        },

        async saveCustomer() {
            if (!this.modal.name.trim()) { this.modalError = 'Name is required.'; return; }
            this.saving = true; this.modalError = '';
            try {
                const res = await fetch('/customers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.modal),
                });
                if (!res.ok) { this.modalError = 'Failed to save.'; return; }
                const customer = await res.json();
                this.customers.push(customer);
                this.selectedId = customer.id;
                this.form.name             = customer.name;
                this.form.email            = customer.email || '';
                this.form.contact_number   = customer.contact_number || '';
                this.form.delivery_address = customer.delivery_address || '';
                this.modal = { name: '', email: '', contact_number: '', delivery_address: '' };
                this.showModal = false;
            } catch(e) {
                this.modalError = 'Network error.';
            } finally {
                this.saving = false;
            }
        }
    }
}

function quotationForm(plans, existing) {
    return {
        plans,
        planId: existing ? existing.plan_id : '',
        isCustomPlan: existing ? existing.is_custom_plan : false,
        planName: existing ? existing.plan_name : '',
        planSpecs: existing ? (existing.plan_specs || '') : '',
        ratePerDay: existing ? parseFloat(existing.rate_per_day) : 0,
        rateType: existing ? (existing.rate_type || 'daily') : 'daily',
        planDailyRate: 0,
        planWeeklyRate: 0,
        planMonthlyRate: 0,
        depositOption: existing ? existing.deposit_option : 'standard',
        depositAmount: existing ? parseFloat(existing.deposit_amount) : 0,
        standardDepositPerUnit: 0,
        quantity: existing ? existing.quantity : 1,
        startDate: existing ? existing.start_date.substring(0, 10) : '',
        endDate: existing ? existing.end_date.substring(0, 10) : '',
        totalDays: existing ? existing.total_days : 0,
        deliveryFee: existing ? parseFloat(existing.delivery_fee) : 0,
        taxPercent: existing ? parseFloat(existing.tax_percent) : 6,
        rentalFee: existing ? parseFloat(existing.rental_fee) : 0,
        taxAmount: existing ? parseFloat(existing.tax_amount) : 0,
        subtotal: existing ? parseFloat(existing.subtotal) : 0,
        totalPayable: existing ? parseFloat(existing.total_payable) : 0,

        init() {
            if (this.planId) {
                const plan = this.plans.find(p => p.id == this.planId);
                if (plan) {
                    this.standardDepositPerUnit = plan.deposit_per_unit;
                    this.planDailyRate   = plan.daily_rate;
                    this.planWeeklyRate  = plan.weekly_rate;
                    this.planMonthlyRate = plan.monthly_rate;
                }
            }
            this.$watch('startDate', () => this.calculate());
            this.$watch('endDate', () => this.calculate());
            if (existing) {
                this.$nextTick(() => {
                    const cp = document.querySelector('[x-data*="customerPicker"]')?._x_dataStack?.[0];
                    if (cp && existing.customer_id) cp.selectedId = String(existing.customer_id);
                });
            }
        },

        parseDate(str) {
            if (!str) return null;
            const p = str.substring(0, 10).split('-');
            return new Date(+p[0], +p[1] - 1, +p[2]);
        },

        rateForType() {
            if (this.rateType === 'weekly')  return this.planWeeklyRate;
            if (this.rateType === 'monthly') return this.planMonthlyRate;
            return this.planDailyRate;
        },

        changeRateType() {
            if (!this.isCustomPlan) this.ratePerDay = this.rateForType();
            this.calculate();
        },

        selectPlan() {
            const plan = this.plans.find(p => p.id == this.planId);
            if (!plan) return;
            this.isCustomPlan = plan.is_custom;
            if (!plan.is_custom) {
                this.planName        = plan.name;
                this.planSpecs       = plan.specs || '';
                this.planDailyRate   = plan.daily_rate;
                this.planWeeklyRate  = plan.weekly_rate;
                this.planMonthlyRate = plan.monthly_rate;
                this.ratePerDay      = this.rateForType();
                this.standardDepositPerUnit = plan.deposit_per_unit;
                if (this.depositOption === 'standard') {
                    this.depositAmount = plan.deposit_per_unit * this.quantity;
                }
            } else {
                this.planName = '';
                this.planSpecs = '';
                this.planDailyRate = this.planWeeklyRate = this.planMonthlyRate = 0;
                this.ratePerDay = 0;
                this.standardDepositPerUnit = 0;
            }
            this.calculate();
        },

        updateDeposit() {
            if (this.depositOption === 'standard') {
                this.depositAmount = this.standardDepositPerUnit * this.quantity;
            }
            this.calculate();
        },

        billingUnits() {
            if (this.rateType === 'weekly')  return Math.ceil(this.totalDays / 7);
            if (this.rateType === 'monthly') return Math.ceil(this.totalDays / 30);
            return this.totalDays;
        },

        calculate() {
            const s = this.parseDate(this.startDate);
            const e = this.parseDate(this.endDate);
            if (s && e && e >= s) {
                this.totalDays = Math.round((e - s) / 86400000) + 1;
            } else {
                this.totalDays = 0;
            }
            const delivery = parseFloat(this.deliveryFee || 0);
            const deposit  = parseFloat(this.depositAmount || 0);
            this.rentalFee    = Math.round(this.ratePerDay * this.quantity * this.billingUnits() * 100) / 100;
            this.subtotal     = Math.round((this.rentalFee + delivery) * 100) / 100;
            this.taxAmount    = Math.round(this.subtotal * (this.taxPercent / 100) * 100) / 100;
            this.totalPayable = Math.round((this.subtotal + this.taxAmount + deposit) * 100) / 100;
            if (this.depositOption === 'standard' && this.standardDepositPerUnit > 0) {
                this.depositAmount = Math.round(this.standardDepositPerUnit * this.quantity * 100) / 100;
            }
        },

        fmt(n) {
            return 'RM ' + parseFloat(n || 0).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }
}
</script>
@endsection
