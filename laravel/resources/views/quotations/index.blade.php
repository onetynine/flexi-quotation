@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Quotations</h1>
        <span class="text-sm text-gray-500">{{ $quotations->total() }} total</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Quotation No</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Customer</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Plan</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Qty</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Start</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">End</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Days</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total (RM)</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Agent</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($quotations as $q)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-semibold text-gray-800">{{ $q->quotation_no }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $q->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $q->contact_number }}</div>
                    </td>
                    <td class="px-4 py-3 max-w-xs">
                        <div class="truncate text-gray-700">{{ $q->plan_name }}</div>
                        @if($q->plan_specs)
                            <div class="text-xs text-gray-500 truncate">{{ $q->plan_specs }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">{{ $q->quantity }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $q->start_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $q->end_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-center">{{ $q->total_days }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($q->total_payable, 2) }}</td>
                    <td class="px-4 py-3">
                        @php
                            $colors = ['Pending' => 'bg-yellow-100 text-yellow-800', 'Accepted' => 'bg-green-100 text-green-800', 'Cancelled' => 'bg-red-100 text-red-800'];
                            $color = $colors[$q->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">{{ $q->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $q->agent_name }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('quotations.show', $q) }}" class="text-blue-600 hover:underline text-xs">View</a>
                            <a href="{{ route('quotations.edit', $q) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
                            <a href="{{ route('quotations.pdf', $q) }}" class="text-green-600 hover:underline text-xs">PDF</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-4 py-10 text-center text-gray-500">No quotations yet. <a href="{{ route('quotations.create') }}" class="text-yellow-600 font-semibold hover:underline">Create one</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($quotations->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $quotations->links() }}
    </div>
    @endif
</div>
@endsection
