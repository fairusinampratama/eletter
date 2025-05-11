@props([
'id',
'title',
'route',
'fields' => [],
'size' => '2xl'
])

<div x-data="{ show: false, item: null }" x-show="show" x-on:edit.window="item = $event.detail[0]; show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $id }}') show = false" x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <!-- Backdrop -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80" aria-hidden="true">
    </div>

    <!-- Modal wrapper -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <!-- Modal header -->
            <div class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                    x-on:click="show = false">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <form method="POST" x-bind:action="'{{ $route }}' + '/' + (item?.id || '')">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" x-bind:value="item?.id">
                    <div class="grid grid-cols-6 gap-6">
                        @foreach($fields as $field)
                        <div class="col-span-6 sm:col-span-{{ $field['colspan'] ?? '3' }}">
                            <label for="edit-{{ $field['name'] }}"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $field['label']
                                }}</label>
                            @if($field['type'] === 'select')
                            <select name="{{ $field['name'] }}" id="edit-{{ $field['name'] }}"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                {{ isset($field['required']) && $field['required'] ? 'required' : '' }}
                                x-model="item?.{{ $field['name'] }}">
                                <option value="">Select {{ strtolower($field['label']) }}</option>
                                @foreach($field['options'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @else
                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                                id="edit-{{ $field['name'] }}"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="{{ $field['placeholder'] ?? 'Enter ' . strtolower($field['label']) }}" {{
                                isset($field['required']) && $field['required'] ? 'required' : '' }}
                                x-model="item?.{{ $field['name'] }}">
                            @endif
                        </div>
                        @endforeach
                    </div>
            </div>
            <!-- Modal footer -->
            <div class="items-center p-6 border-t border-gray-200 rounded-b dark:border-gray-700">
                <button type="submit"
                    class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    Submit
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
