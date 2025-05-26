<x-head />
<x-body>
    <section
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-blue-200 to-blue-300 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
        <div class="flex flex-col items-center justify-center px-4 py-8 mx-auto w-full max-w-md">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-10 h-10 mr-3" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg"
                    alt="logo">
                <span>E-Letter UKM UIN Malang</span>
            </a>
            <div
                class="w-full bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <x-alerts.flash-messages />
                <div class="mb-6 text-center">
                    <h1 class="text-2xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white mb-1">
                        Welcome Back!</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sign in to your account to continue</p>
                </div>
                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5" autocomplete="off">
                    @csrf
                    <div class="flex flex-col gap-2">
                        <label for="username" class="text-sm font-medium text-gray-900 dark:text-white">Username</label>
                        <input type="text" name="username" id="username" autocomplete="username"
                            class="h-12 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full px-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="your username" required value="{{ old('username') }}">
                        @if ($errors->has('username'))
                        <span class="text-xs text-red-700 dark:text-red-300">{{ $errors->first('username') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2" x-data="{ show: false }">
                        <label for="password" class="text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" id="password"
                                autocomplete="current-password" placeholder="••••••••"
                                class="h-12 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full px-4 pr-12 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center px-3 cursor-pointer text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none"
                                style="height: 100%;">
                                <template x-if="!show">
                                    <!-- Heroicons Eye -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                    </svg>
                                </template>
                                <template x-if="show">
                                    <!-- Heroicons Eye Slash -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 002.25 12s3.75 7.5 9.75 7.5c1.772 0 3.37-.344 4.75-.927" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.228 6.228A10.477 10.477 0 0112 4.5c6 0 9.75 7.5 9.75 7.5a10.478 10.478 0 01-1.272 2.011M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                        <span class="text-xs text-red-600 mt-1">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <!-- Forgot password link removed -->
                    </div>
                    <div class="flex flex-col gap-2 pt-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center w-full h-12 px-6 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors duration-200">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-body>
