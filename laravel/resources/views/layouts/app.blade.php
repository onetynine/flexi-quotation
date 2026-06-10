<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Rental - Flexi Quotation</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-yellow-400 shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('quotations.index') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.svg') }}" alt="Smart Rental" class="h-10 w-auto">
                </a>
                <div class="flex items-center gap-1">
                    <a href="{{ route('quotations.index') }}"
                       class="px-3 py-1.5 rounded text-sm font-semibold {{ request()->routeIs('quotations.*') ? 'bg-gray-800 text-white' : 'text-gray-800 hover:bg-yellow-500' }} transition">
                        Quotations
                    </a>
                    <a href="{{ route('customers.index') }}"
                       class="px-3 py-1.5 rounded text-sm font-semibold {{ request()->routeIs('customers.*') ? 'bg-gray-800 text-white' : 'text-gray-800 hover:bg-yellow-500' }} transition">
                        Customers
                    </a>
                    <a href="{{ route('plans.index') }}"
                       class="px-3 py-1.5 rounded text-sm font-semibold {{ request()->routeIs('plans.*') ? 'bg-gray-800 text-white' : 'text-gray-800 hover:bg-yellow-500' }} transition">
                        Plans
                    </a>
                    <a href="{{ route('settings.index') }}"
                       class="px-3 py-1.5 rounded text-sm font-semibold {{ request()->routeIs('settings.*') ? 'bg-gray-800 text-white' : 'text-gray-800 hover:bg-yellow-500' }} transition">
                        Settings
                    </a>
                </div>
            </div>
            <a href="{{ route('quotations.create') }}"
               class="bg-gray-800 text-white px-4 py-2 rounded font-semibold hover:bg-gray-700 transition text-sm">
                + New Quotation
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
