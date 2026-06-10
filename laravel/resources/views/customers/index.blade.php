@extends('layouts.app')
@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Customers</h1>
        <a href="{{ route('customers.create') }}" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold px-4 py-2 rounded text-sm transition">+ New Customer</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Contact</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Address</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Quotations</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $c->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $c->email ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $c->contact_number ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $c->delivery_address ?: '—' }}</td>
                    <td class="px-4 py-3 text-center">{{ $c->quotations()->count() }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('customers.edit', $c) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Delete customer?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">No customers. <a href="{{ route('customers.create') }}" class="text-yellow-600 font-semibold hover:underline">Add one</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="px-6 py-4 border-t">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
