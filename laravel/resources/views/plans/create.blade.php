@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('plans.index') }}" class="hover:text-gray-800">Plans</a>
        <span>/</span><span class="text-gray-800 font-medium">New Plan</span>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-lg font-bold text-gray-800 mb-4">New Plan</h1>
        <form method="POST" action="{{ route('plans.store') }}">
            @csrf
            @include('plans._form')
            <div class="mt-6 flex gap-3">
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-6 py-2 rounded transition">Save</button>
                <a href="{{ route('plans.index') }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
