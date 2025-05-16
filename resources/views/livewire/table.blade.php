@props([
'model' => null,
'routePrefix' => null,
'columns' => [],
'actions' => [],
'searchable' => true,
'withRelations' => [],
'bulkActions' => [],
'selectable' => false,
])

<div>
    @if(!empty($search) && $searchable)
    <div
        class="px-4 py-2 text-xs text-blue-800 dark:text-blue-400 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="flex items-center">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 9a2 2 0 114 0 2 2 0 01-4 0z"></path>
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a4 4 0 00-3.446 6.032l-2.261 2.26a1 1 0 101.414 1.415l2.261-2.261A4 4 0 1011 5z"
                    clip-rule="evenodd"></path>
            </svg>
            <span>
                {{ $data->total() }} results found for
                "<span class="font-medium">{{ $search }}</span>"
            </span>
        </div>
    </div>
    @endif

    <div class="flex flex-col">
        {{-- Bulk Actions --}}
        @if($selectable && count($bulkActions) > 0 && count($selected) > 0)
        <div
            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ count($selected) }} items selected
                </span>
            </div>
            <div class="flex items-center space-x-2">
                @foreach($bulkActions as $action)
                <button wire:click="$dispatch('delete', { ids: {{ json_encode($selected) }} })"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg {{ $action['type'] === 'delete' ? 'bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' : 'bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800' }}">
                    @if($action['type'] === 'delete')
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    @endif
                    {{ $action['label'] }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                @if($selectable)
                                <th scope="col" class="p-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectAll"
                                            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                </th>
                                @endif
                                @foreach($columns as $column)
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    <div class="flex items-center space-x-1 cursor-pointer group"
                                        wire:click="sort('{{ $column['field'] }}')">
                                        <span
                                            class="{{ $sortField === $column['field'] ? 'text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300' }}">{{
                                            $column['label'] }}</span>
                                        <div class="flex flex-col space-y-0.5">
                                            <svg class="w-3 h-3 transition-all duration-200 {{ $sortField === $column['field'] && $sortDirection === 'asc' ? 'text-primary-600 dark:text-primary-400 scale-110' : 'text-gray-400 opacity-50 group-hover:opacity-100' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                            <svg class="w-3 h-3 transition-all duration-200 {{ $sortField === $column['field'] && $sortDirection === 'desc' ? 'text-primary-600 dark:text-primary-400 scale-110' : 'text-gray-400 opacity-50 group-hover:opacity-100' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </th>
                                @endforeach
                                @if(count($actions) > 0)
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Aksi
                                </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($data as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                @if($selectable)
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}"
                                            class="w-4 h-4 text-primary-600 bg-gray-100 border border-gray-300 rounded focus:ring-primary-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                                    </div>
                                </td>
                                @endif
                                @foreach($columns as $column)
                                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                                        @if(isset($column['type']) && $column['type'] === 'number')
                                        {{ ($data->currentPage() - 1) * $data->perPage() + $loop->parent->iteration }}
                                        @elseif(isset($column['type']) && $column['type'] === 'component')
                                        @if($column['component'] === 'signing-status')
                                        <div class="flex flex-col space-y-2">
                                            @foreach($item->signatures()->with('signer.role')->orderBy('order')->get()
                                            as $signature)
                                            <div class="flex items-center space-x-2">
                                                @if($signature->signed_at)
                                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                @else
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                @endif
                                                <span class="text-sm">
                                                    {{ $signature->signer->fullname }}
                                                    <span class="text-xs text-gray-500">({{
                                                        $signature->signer->role->name }})</span>
                                                </span>
                                            </div>
                                            @endforeach
                                        </div>
                                        @else
                                        <x-dynamic-component :component="$column['component']" :letter="$item" />
                                        @endif
                                        @else
                                        {{ data_get($item, $column['field']) }}
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                                @if(count($actions) > 0)
                                <td class="p-4 space-x-2 whitespace-nowrap">
                                    @foreach($availableActions[$item->id] as $action)
                                    @if($action['type'] === 'edit')
                                    <button wire:click="edit({{ $item->id }})"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $action['label'] ?? 'Edit' }}
                                    </button>
                                    @elseif($action['type'] === 'delete')
                                    <button wire:click="$dispatch('delete', { ids: [{{ $item->id }}] })"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $action['label'] ?? 'Delete' }}
                                    </button>
                                    @elseif($action['type'] === 'view')
                                    <button wire:click="view({{ $item->id }})"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $action['label'] ?? 'View' }}
                                    </button>
                                    @elseif($action['type'] === 'confirm')
                                    <button wire:click="confirm({{ $item->id }})"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-emerald-500 hover:bg-emerald-600 focus:ring-4 focus:ring-emerald-300 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                                                fill="none" />
                                            <path d="M8 12.5l2.5 2.5 5-5" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                        </svg>
                                        {{ $action['label'] ?? 'Confirm' }}
                                    </button>
                                    @endif
                                    @endforeach
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0">
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $data->firstItem() }}-{{
                    $data->lastItem() }}</span>
                of <span class="font-semibold text-gray-900 dark:text-white">{{ $data->total() }}</span>
            </span>
        </div>
        <div class="flex items-center space-x-3">
            @if($data->previousPageUrl())
            <button wire:click="previousPage"
                class="inline-flex items-center justify-center flex-1 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                <svg class="w-5 h-5 mr-1 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd"></path>
                </svg>
                Previous
            </button>
            @endif
            @if($data->nextPageUrl())
            <button wire:click="nextPage"
                class="inline-flex items-center justify-center flex-1 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                Next
                <svg class="w-5 h-5 ml-1 -mr-1" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            @endif
        </div>
    </div>
</div>