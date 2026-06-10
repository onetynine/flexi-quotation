@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('quotations.index') }}" class="hover:text-gray-800">Quotations</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ $quotation->quotation_no }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('quotations.edit', $quotation) }}"
               class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold px-4 py-2 rounded text-sm transition">Edit</a>
            <a href="{{ route('quotations.pdf', $quotation) }}"
               class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded text-sm transition">Download PDF</a>
            <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
                  onsubmit="return confirm('Delete this quotation?')">
                @csrf @method('DELETE')
                <button class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded text-sm transition">Delete</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow divide-y divide-gray-100">
        {{-- Header --}}
        <div class="p-6 flex items-start justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ $quotation->quotation_no }}</div>
                <div class="text-sm text-gray-500 mt-1">Issued {{ $quotation->created_at->format('d M Y') }}</div>
            </div>
            @php
                $colors = ['Pending' => 'bg-yellow-100 text-yellow-800', 'Accepted' => 'bg-green-100 text-green-800', 'Cancelled' => 'bg-red-100 text-red-800'];
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-bold {{ $colors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $quotation->status }}
            </span>
        </div>

        {{-- Customer --}}
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Customer</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $quotation->customer_name }}</span></div>
                <div><span class="text-gray-500">Email:</span> {{ $quotation->email ?: '—' }}</div>
                <div><span class="text-gray-500">Contact:</span> {{ $quotation->contact_number ?: '—' }}</div>
                <div><span class="text-gray-500">Address:</span> {{ $quotation->delivery_address ?: '—' }}</div>
            </div>
        </div>

        {{-- Plan --}}
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Plan</h3>
            <div class="text-sm space-y-1">
                <div><span class="text-gray-500">Plan:</span> <span class="font-semibold">{{ $quotation->plan_name }}</span></div>
                @if($quotation->plan_specs)
                <div><span class="text-gray-500">Specs:</span> {{ $quotation->plan_specs }}</div>
                @endif
                <div><span class="text-gray-500">Quantity:</span> {{ $quotation->quantity }} units</div>
                <div><span class="text-gray-500">Rate:</span> RM {{ number_format($quotation->rate_per_day, 2) }}/day</div>
            </div>
        </div>

        {{-- Duration --}}
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Rental Duration</h3>
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div><span class="text-gray-500">Start:</span> <span class="font-medium">{{ $quotation->start_date->format('d M Y') }}</span></div>
                <div><span class="text-gray-500">End:</span> <span class="font-medium">{{ $quotation->end_date->format('d M Y') }}</span></div>
                <div><span class="text-gray-500">Days:</span> <span class="font-bold">{{ $quotation->total_days }} days</span></div>
            </div>
        </div>

        {{-- Financials --}}
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Financials</h3>
            <div class="space-y-2 text-sm max-w-sm ml-auto">
                <div class="flex justify-between"><span class="text-gray-500">Rental Fee</span><span>RM {{ number_format($quotation->rental_fee, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Deposit</span><span>RM {{ number_format($quotation->deposit_amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Delivery ({{ $quotation->delivery_option }})</span><span>RM {{ number_format($quotation->delivery_fee, 2) }}</span></div>
                <div class="flex justify-between border-t pt-2"><span class="text-gray-500">Subtotal</span><span>RM {{ number_format($quotation->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tax ({{ $quotation->tax_percent }}%)</span><span>RM {{ number_format($quotation->tax_amount, 2) }}</span></div>
                <div class="flex justify-between border-t-2 border-gray-400 pt-2 font-bold text-base">
                    <span>Total Payable</span>
                    <span class="text-yellow-600">RM {{ number_format($quotation->total_payable, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Agent --}}
        @if($quotation->agent_name)
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Agent</h3>
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div><span class="text-gray-500">Name:</span> {{ $quotation->agent_name }}</div>
                <div><span class="text-gray-500">Contact:</span> {{ $quotation->agent_contact ?: '—' }}</div>
                <div><span class="text-gray-500">Email:</span> {{ $quotation->agent_email ?: '—' }}</div>
            </div>
        </div>
        @endif

        @if($quotation->quotation_link)
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Quotation Link</h3>
            <a href="{{ $quotation->quotation_link }}" target="_blank" class="text-blue-600 hover:underline text-sm break-all">{{ $quotation->quotation_link }}</a>
        </div>
        @endif
    </div>
</div>
@endsection
