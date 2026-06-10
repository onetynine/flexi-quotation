@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('quotations.index') }}" class="hover:text-gray-800">Quotations</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">New Quotation</span>
    </div>

    <form method="POST" action="{{ route('quotations.store') }}"
          x-data="quotationForm({{ $plans->toJson() }})"
          @submit.prevent="submitForm">
        @csrf
        @include('quotations._form')
        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-6 py-2 rounded transition">
                Create Quotation
            </button>
            <a href="{{ route('quotations.index') }}"
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
            } else {
                this.form = { name: '', email: '', contact_number: '', delivery_address: '' };
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            || document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]?.replace(/%3D/g, '=') || '',
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

function quotationForm(plans) {
    return {
        plans,
        planId: '',
        isCustomPlan: false,
        planName: '',
        planSpecs: '',
        ratePerDay: 0,
        depositOption: 'standard',
        depositAmount: 0,
        standardDepositPerUnit: 0,
        quantity: 1,
        startDate: '',
        endDate: '',
        totalDays: 0,
        deliveryFee: 0,
        taxPercent: 6,
        rentalFee: 0,
        taxAmount: 0,
        subtotal: 0,
        totalPayable: 0,

        init() {
            this.$watch('startDate', () => this.calculate());
            this.$watch('endDate', () => this.calculate());
        },

        parseDate(str) {
            if (!str) return null;
            const p = str.substring(0, 10).split('-');
            return new Date(+p[0], +p[1] - 1, +p[2]);
        },

        selectPlan() {
            const plan = this.plans.find(p => p.id == this.planId);
            if (!plan) return;

            this.isCustomPlan = plan.is_custom;
            if (!plan.is_custom) {
                this.planName = plan.name;
                this.planSpecs = plan.specs || '';
                this.ratePerDay = plan.daily_rate;
                this.standardDepositPerUnit = plan.deposit_per_unit;
                if (this.depositOption === 'standard') {
                    this.depositAmount = plan.deposit_per_unit * this.quantity;
                }
            } else {
                this.planName = '';
                this.planSpecs = '';
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
            this.rentalFee    = Math.round(this.ratePerDay * this.quantity * this.totalDays * 100) / 100;
            this.subtotal     = Math.round((this.rentalFee + delivery) * 100) / 100;
            this.taxAmount    = Math.round(this.subtotal * (this.taxPercent / 100) * 100) / 100;
            this.totalPayable = Math.round((this.subtotal + this.taxAmount + deposit) * 100) / 100;

            if (this.depositOption === 'standard' && this.standardDepositPerUnit > 0) {
                this.depositAmount = Math.round(this.standardDepositPerUnit * this.quantity * 100) / 100;
            }
        },

        fmt(n) {
            return 'RM ' + parseFloat(n || 0).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        },

        submitForm() {
            this.$el.submit();
        }
    }
}
</script>
@endsection
