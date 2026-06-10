@extends('layouts.app')
@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Plans</h1>
        <a href="{{ route('plans.create') }}" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold px-4 py-2 rounded text-sm transition">+ New Plan</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Plan Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Specs</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Deposit/Unit</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Daily</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Weekly</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Monthly</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Type</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($plans as $plan)
                <tr class="hover:bg-gray-50 {{ !$plan->active ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $plan->name }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $plan->specs ?: '—' }}</td>
                    <td class="px-4 py-3 text-right">{{ $plan->is_custom ? '—' : 'RM '.number_format($plan->deposit_per_unit, 0) }}</td>
                    <td class="px-4 py-3 text-right">{{ $plan->is_custom ? '—' : 'RM '.number_format($plan->daily_rate, 0) }}</td>
                    <td class="px-4 py-3 text-right">{{ $plan->is_custom ? '—' : 'RM '.number_format($plan->weekly_rate, 0) }}</td>
                    <td class="px-4 py-3 text-right">{{ $plan->is_custom ? '—' : 'RM '.number_format($plan->monthly_rate, 0) }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($plan->is_custom)
                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">Custom</span>
                        @else
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Standard</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($plan->active)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('plans.edit', $plan) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('plans.destroy', $plan) }}" onsubmit="return confirm('Delete plan?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-10 text-center text-gray-500">No plans. <a href="{{ route('plans.create') }}" class="text-yellow-600 font-semibold hover:underline">Add one</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
