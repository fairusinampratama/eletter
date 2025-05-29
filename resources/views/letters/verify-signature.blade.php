<x-head />

<x-alerts.flash-messages />

<x-body>
    <div
        class="flex items-center justify-center min-h-screen py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-2xl w-full mx-auto">
            <div
                class="bg-white rounded-xl shadow-sm p-4 sm:p-6 lg:p-8 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Verify Signature</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload your PDF document to verify this
                            specific signature</p>
                    </div>
                    <button id="theme-toggle" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6 sm:mb-8">
                    <div class="flex items-center p-3 sm:p-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 border border-blue-200 dark:border-blue-900"
                        role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 sm:w-5 sm:h-5 me-2 sm:me-3" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <div class="flex-1">
                            <span class="font-medium">Verification ID:</span>
                            <span
                                class="ml-2 font-mono text-xs sm:text-sm bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">{{
                                $verification_id }}</span>
                        </div>
                    </div>
                </div>

                @if(isset($signature) && isset($signer))
                <div class="mb-6 sm:mb-8">
                    <div
                        class="p-3 sm:p-4 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3">Signature
                            Details</h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Signer</p>
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $signer->fullname }}</p>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $signer->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                        {{ $signer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="mt-2 space-y-1">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $signer->role->name }}
                                    </div>
                                    @if($signer->institution)
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        {{ $signer->institution->name }}
                                    </div>
                                    @endif
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Year: {{ $signer->year }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Signature Hash</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="relative">
                                            <div
                                                class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-8 h-8 bg-primary-600 rounded flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-white" aria-hidden="true"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 20 20">
                                                                <path stroke="currentColor" stroke-linecap="round"
                                                                    stroke-linejoin="round" stroke-width="2"
                                                                    d="M7.5 3.5h6A1.5 1.5 0 0 1 15 5v6a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 6 11V5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                                                <path stroke="currentColor" stroke-linecap="round"
                                                                    stroke-linejoin="round" stroke-width="2"
                                                                    d="M4.5 6.5h6A1.5 1.5 0 0 1 12 8v6a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 3 14V8a1.5 1.5 0 0 1 1.5-1.5Z" />
                                                            </svg>
                                                        </div>
                                                        <p class="font-mono text-xs sm:text-sm text-gray-900 dark:text-white truncate"
                                                            title="{{ $signature }}">
                                                            {{ substr($signature, 0, 16) }}...{{ substr($signature, -16)
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    onclick="navigator.clipboard.writeText('{{ $signature }}').then(() => { this.querySelector('span').textContent = 'Copied!'; setTimeout(() => { this.querySelector('span').textContent = 'Copy'; }, 2000); })"
                                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-500 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                                    <svg class="w-3 h-3 mr-1.5" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M7.5 3.5h6A1.5 1.5 0 0 1 15 5v6a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 6 11V5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M4.5 6.5h6A1.5 1.5 0 0 1 12 8v6a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 3 14V8a1.5 1.5 0 0 1 1.5-1.5Z" />
                                                    </svg>
                                                    <span>Copy</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('verify.signature.check', [$verification_id, $signature]) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                    @csrf
                    <div>
                        <label for="file" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload
                            PDF Document</label>
                        <div x-data="{ fileName: null, error: null }" class="space-y-2">
                            <div class="flex items-center justify-center w-full">
                                <label for="file"
                                    class="flex flex-col items-center justify-center w-full h-48 sm:h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 mb-2 sm:mb-3 text-gray-400"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-1 sm:mb-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                            <span class="font-semibold">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF files only (MAX. 10MB)
                                        </p>
                                    </div>
                                    <input id="file" name="file" type="file" class="hidden" accept=".pdf" required
                                        x-on:change="
                                            const file = $event.target.files[0];
                                            fileName = file?.name;
                                            error = null;

                                            if (file) {
                                                if (!file.name.toLowerCase().endsWith('.pdf')) {
                                                    error = 'Only PDF files are allowed';
                                                    $event.target.value = '';
                                                    fileName = null;
                                                } else if (file.size > 10 * 1024 * 1024) {
                                                    error = 'File size must be less than 10MB';
                                                    $event.target.value = '';
                                                    fileName = null;
                                                }
                                            }
                                        " />
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
                            <div x-show="error" class="text-sm text-red-600 dark:text-red-400" x-text="error"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Supported format: PDF (Max size: 10MB)
                        </p>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-4 sm:px-5 py-2 sm:py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Verify Signature
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-body>