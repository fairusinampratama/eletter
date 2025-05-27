@props([
'id',
'title',
'route',
'fields' => [],
'size' => '2xl'
])

<div x-data="{ show: false }" x-show="show" x-on:open-modal.window="if ($event.detail === '{{ $id }}') show = true"
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
                <form method="POST" action="{{ $route }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-6 gap-6">
                        @foreach($fields as $field)
                        <div class="col-span-6 sm:col-span-{{ $field['colspan'] ?? '3' }}">
                            @if($field['type'] === 'toggle')
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="{{ $field['name'] }}"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $field['label'] }}
                                        @if(isset($field['required']) && $field['required'])
                                        <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $field['label_text'] }}</p>
                                    @if(isset($field['helper']))
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $field['helper'] }}</p>
                                    @endif
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $field['name'] }}" value="{{ $field['value'] }}"
                                        class="sr-only peer" {{ isset($field['disabled']) && $field['disabled']
                                        ? 'disabled' : '' }} {{ isset($field['checked']) && $field['checked']
                                        ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                    </div>
                                </label>
                            </div>
                            @else
                            <label for="{{ $field['name'] }}"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $field['label'] }}
                                @if(isset($field['required']) && $field['required'])
                                <span class="text-red-500">*</span>
                                @endif
                            </label>
                            @if($field['type'] === 'select')
                            <select name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                {{ isset($field['required']) && $field['required'] ? 'required' : '' }}
                                @if(isset($field['attributes'])) @foreach($field['attributes'] as $attr=> $value)
                                {{ $attr }}="{{ $value }}"
                                @endforeach
                                @endif
                                @if(isset($field['value']))
                                value="{{ $field['value'] }}"
                                @endif>
                                <option value="">Pilih {{ strtolower($field['label']) }}</option>
                                @foreach($field['options'] as $value => $label)
                                <option value="{{ $value }}" {{ isset($field['value']) && $field['value']==$value
                                    ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @elseif($field['type'] === 'file')
                            <div x-data="{ fileName: null }" class="space-y-2">
                                <div class="flex items-center justify-center w-full">
                                    <label for="{{ $field['name'] }}"
                                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $field['accept'] ?? 'PDF' }} (MAX. 10MB)
                                            </p>
                                        </div>
                                        <input type="file" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                            class="hidden" {{ isset($field['required']) && $field['required']
                                            ? 'required' : '' }} @if(isset($field['accept']))
                                            accept="{{ $field['accept'] }}" @endif
                                            x-on:change="fileName = $event.target.files[0]?.name" />
                                    </label>
                                </div>
                                <div x-show="fileName"
                                    class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span x-text="fileName"></span>
                                </div>
                                @if(isset($field['helper']))
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $field['helper'] }}</p>
                                @endif
                            </div>
                            @elseif($field['type'] === 'date')
                            <input type="date" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="{{ $field['placeholder'] ?? '' }}" {{ isset($field['required']) &&
                                $field['required'] ? 'required' : '' }}>
                            @elseif($field['type'] === 'year')
                            <input type="number" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="{{ $field['placeholder'] ?? '' }}" min="2000" max="{{ date('Y') + 1 }}"
                                value="{{ date('Y') }}" {{ isset($field['required']) && $field['required'] ? 'required'
                                : '' }}>
                            @elseif($field['type'] === 'hidden')
                            <input type="hidden" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                value="{{ $field['value'] ?? '' }}">
                            @else
                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="{{ $field['placeholder'] ?? '' }}" {{ isset($field['required']) &&
                                $field['required'] ? 'required' : '' }} @if(isset($field['maxlength']))
                                maxlength="{{ $field['maxlength'] }}" @endif @if(isset($field['minlength']))
                                minlength="{{ $field['minlength'] }}" @endif @if(isset($field['pattern']))
                                pattern="{{ $field['pattern'] }}" @endif @if(isset($field['title']))
                                title="{{ $field['title'] }}" @endif @if(isset($field['oninput']))
                                oninput="{{ $field['oninput'] }}" @endif>
                            @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
            </div>
            <!-- Modal footer -->
            <div
                class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-700">
                <button type="button"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                    x-on:click="show = false">Batal</button>
                <button type="submit"
                    class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>