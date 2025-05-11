@props([
'title' => '',
'breadcrumbItems' => [],
'showFilter' => false,
'filterRoute' => null,
'filterPlaceholder' => 'Search...',
'showAddButton' => false,
'addButtonText' => 'Add New',
'addButtonRoute' => null,
'addButtonId' => null,
])

<div class="p-4 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex flex-col space-y-4">
        {{-- Header Section --}}
        <div class="flex flex-col space-y-2">
            @if(count($breadcrumbItems) > 0)
            <x-dashboard.breadcrumb :items="$breadcrumbItems" />
            @endif
            @if($title)
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ $title }}</h1>
            @endif
        </div>

        {{-- Filter Section --}}
        @if($showFilter)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form class="w-full sm:w-auto" action="{{ route($filterRoute) }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full sm:w-64 lg:w-96 bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="{{ $filterPlaceholder }}">
                    @if(request()->has('search') && request('search'))
                    <a href="{{ route($filterRoute) }}"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                    @endif
                </div>
            </form>

            @if($showAddButton)
            <button x-data x-on:click="$dispatch('open-modal', '{{ $addButtonId }}')"
                class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                {{ $addButtonText }}
            </button>
            @endif
        </div>
        @endif
    </div>
</div>

{{ $slot }}
