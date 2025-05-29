<x-head>
    <title>Verification Result - {{ $letter->code }}</title>
</x-head>

<x-body>
    <div
        class="flex items-center justify-center min-h-screen py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-2xl w-full mx-auto">
            <div
                class="bg-white rounded-xl shadow-sm p-4 sm:p-6 lg:p-8 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Verification Result</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Document verification details and
                            status</p>
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

                <div class="space-y-4 sm:space-y-6">
                    <div
                        class="p-3 sm:p-4 rounded-lg bg-blue-50 dark:bg-gray-800 border border-blue-200 dark:border-blue-900">
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3">Letter Details
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Code</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $letter->code }}</p>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Date</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $letter->date }}</p>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Category</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $letter->category->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Verification ID</p>
                                <p
                                    class="font-mono text-xs sm:text-sm bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded inline-block">
                                    {{ $letter->verification_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3">Verification
                            Status</h2>
                        <div
                            class="p-3 sm:p-4 rounded-lg {{ $valid ? 'bg-green-50 border border-green-200 dark:border-green-900' : 'bg-red-50 border border-red-200 dark:border-red-900' }} dark:bg-gray-800">
                            @if($valid)
                            <div class="flex items-center text-green-700 dark:text-green-400">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <p class="font-medium">Letter is valid and all signatures are authentic</p>
                            </div>
                            @else
                            <div class="flex items-center text-red-700 dark:text-red-400">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <p class="font-medium">Letter verification failed</p>
                            </div>
                            @if(!empty($unsignedSignatures))
                            <div class="mt-2 text-xs sm:text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Waiting for signatures from:</p>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ implode(', ', $unsignedSignatures)
                                    }}</p>
                            </div>
                            @endif
                            @if(!empty($invalidSignatures))
                            <div class="mt-2 text-xs sm:text-sm">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Invalid signatures from:</p>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ implode(', ', $invalidSignatures) }}
                                </p>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3">Signatures
                        </h2>
                        <div class="space-y-4 w-full">
                            @foreach($letter->signatures as $signature)
                            @php
                            $isInvalid = isset($signatureReasons[$signature->id]);
                            $isUnsigned = !$signature->signed_at || !$signature->signature;
                            $statusColor = $isInvalid || $isUnsigned
                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                            $statusText = $isUnsigned
                            ? 'Not signed yet'
                            : ($isInvalid ? 'Invalid Signature' : ($signature->signed_at instanceof \Carbon\Carbon
                            ? $signature->signed_at->format('M d, Y H:i')
                            : \Carbon\Carbon::parse($signature->signed_at)->format('M d, Y H:i')));
                            @endphp
                            <div
                                class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200">
                                <div class="p-4 sm:p-5">
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 sm:gap-4">
                                        <div class="flex items-start gap-3 sm:gap-4 w-full">
                                            <div class="flex-shrink-0 mt-1">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-800 dark:text-white"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-width="2"
                                                            d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p
                                                        class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                        {{ $signature->signer->fullname }}
                                                    </p>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $signature->signer->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                        {{ $signature->signer->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                                <div class="mt-2 space-y-1">
                                                    <div
                                                        class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        {{ $signature->signer->role->name }}
                                                    </div>
                                                    @if($signature->signer->isCommitteeChairman() ||
                                                    $signature->signer->isCommitteeSecretary())
                                                    @php
                                                    $committee = $signature->signer->committeesAsChairman->first() ??
                                                    $signature->signer->committeesAsSecretary->first();
                                                    @endphp
                                                    @if($committee)
                                                    <div
                                                        class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                        {{ $committee->name }}
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @if($signature->signer->institution)
                                                    <div
                                                        class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                        {{ $signature->signer->institution->name }}
                                                    </div>
                                                    @endif
                                                    <div
                                                        class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        Year: {{ $signature->signer->year }}
                                                    </div>
                                                </div>
                                                @if($isInvalid)
                                                <div class="mt-3 p-2 bg-red-50 dark:bg-red-900/30 rounded-lg">
                                                    <p class="text-sm text-red-600 dark:text-red-400">
                                                        {{ $signatureReasons[$signature->id] }}
                                                    </p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 mt-2 sm:mt-0 w-full sm:w-auto">
                                            @if($signature->signed_at)
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColor }} text-center w-full sm:w-auto">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $statusText }}
                                            </span>
                                            @else
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 text-center w-full sm:w-auto">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Not signed yet
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-start pt-4">
                        <a href="{{ url()->previous() }}"
                            class="inline-flex items-center text-xs sm:text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Back to Verification
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-body>