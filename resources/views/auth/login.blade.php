<x-head />
<x-body>
    <section class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg"
                    alt="logo">
                E-Letter UKM UIN Malang
            </a>
            <div
                class="w-full max-w-xl bg-white rounded-lg shadow dark:border md:mt-0 xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Sign in to your account
                    </h1>
                    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label for="username"
                                class="text-sm font-medium text-gray-900 dark:text-white">Username</label>
                            <input type="text" name="username" id="username"
                                class="h-12 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full px-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="your username" required value="{{ old('username') }}">
                            @if ($errors->has('username'))
                                <span class="text-sm text-red-700 dark:text-red-300">{{ $errors->first('username') }}</span>

                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="password"
                                class="text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                class="h-12 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 w-full px-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            @if ($errors->has('password'))
                                <span class="text-sm text-red-600 mt-2">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="inline-flex items-center justify-center w-full h-12 px-6 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                Sign in
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-body>