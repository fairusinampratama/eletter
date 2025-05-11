@php
use Illuminate\Support\Facades\Route;
@endphp

<x-head />

<x-body>

    <x-dashboard.header />

    <!-- Push content below header -->
    <div class="pt-16 flex">
        <!-- Sidebar (fixed height under header) -->
        <x-dashboard.sidebar :menuItems="$menuItems" />
        <!-- Main content -->
        <main class="w-full lg:ml-64">

            @yield('content')

        </main>
    </div>
</x-body>