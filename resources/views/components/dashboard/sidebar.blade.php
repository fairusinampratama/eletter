@props(['menuItems'])

<aside id="sidebar"
    class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 w-64 h-full pt-16 font-normal duration-75 transition-transform -translate-x-full lg:translate-x-0 lg:flex bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pt-5 pb-4 overflow-y-auto">
        <ul class="space-y-4 font-medium">
            @foreach($menuItems as $item)
                <li>
                    @php
                        // Determine if the current menu item is active by comparing current URL with the item's URL
                        $isActive = url()->current() === url($item['url'] ?? '#');
                    @endphp
                    <a href="{{ $item['url'] ?? '#' }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ $isActive ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        @if(isset($item['icon']))
                            {!! $item['icon'] !!}
                        @endif
                        <span class="flex-1 ms-3 whitespace-nowrap">{{ $item['title'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
