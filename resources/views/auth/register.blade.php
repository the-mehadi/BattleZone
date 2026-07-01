<x-guest-layout>
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Create Account</p>
        <h2 class="mt-2 text-3xl font-black text-white">{{ __('Join BattleZone') }}</h2>
        <p class="mt-3 text-sm leading-6 text-slate-400">
            Register your player account to join Free Fire rooms, build squads, and compete for tournament prizes.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div>
            <label for="name" class="block text-sm font-semibold text-slate-200">{{ __('Name') }}</label>
            <input
                id="name"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter your full name"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="phone" class="block text-sm font-semibold text-slate-200">{{ __('Phone') }}</label>
            <input
                id="phone"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                type="text"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="Enter your phone number"
                required
                autocomplete="tel"
            >
            @error('phone')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4" x-data="{ showPassword: false }">
            <label for="password" class="block text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
            <div class="relative mt-2">
                <input
                    id="password"
                    class="block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 pr-12 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    placeholder="Create a strong password"
                    required
                    autocomplete="new-password"
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-orange-300"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                >
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l2.14 2.14A12.728 12.728 0 0 0 1.44 9.5a.75.75 0 0 0 0 .5C3.3 13.66 7.26 16.5 12 16.5c1.96 0 3.78-.48 5.35-1.32l3.12 3.12a.75.75 0 1 0 1.06-1.06l-18-18ZM9.9 10.96a2.25 2.25 0 0 0 3.14 3.14l-3.14-3.14ZM12 6.75c3.66 0 6.79 2.12 8.25 5.25a10.96 10.96 0 0 1-1.81 2.6l-1.56-1.56a3.75 3.75 0 0 0-5.92-4.52L9.19 6.75c.87-.24 1.81-.37 2.81-.37Z" />
                        <path d="M12 9a3 3 0 0 1 3 3 .75.75 0 0 1-1.5 0 1.5 1.5 0 0 0-1.5-1.5.75.75 0 0 1 0-1.5Z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1.44 12.25a.75.75 0 0 1 0-.5C3.3 8.09 7.26 5.25 12 5.25s8.7 2.84 10.56 6.5a.75.75 0 0 1 0 .5C20.7 15.91 16.74 18.75 12 18.75S3.3 15.91 1.44 12.25ZM12 8.25A3.75 3.75 0 1 0 12 15.75 3.75 3.75 0 0 0 12 8.25Zm0 1.5A2.25 2.25 0 1 1 12 14.25 2.25 2.25 0 0 1 12 9.75Z" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4" x-data="{ showConfirmPassword: false }">
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-200">{{ __('Confirm Password') }}</label>
            <div class="relative mt-2">
                <input
                    id="password_confirmation"
                    class="block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 pr-12 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    name="password_confirmation"
                    placeholder="Confirm your password"
                    required
                    autocomplete="new-password"
                >
                <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-orange-300"
                    :aria-label="showConfirmPassword ? 'Hide confirm password' : 'Show confirm password'"
                >
                    <svg x-show="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l2.14 2.14A12.728 12.728 0 0 0 1.44 9.5a.75.75 0 0 0 0 .5C3.3 13.66 7.26 16.5 12 16.5c1.96 0 3.78-.48 5.35-1.32l3.12 3.12a.75.75 0 1 0 1.06-1.06l-18-18ZM9.9 10.96a2.25 2.25 0 0 0 3.14 3.14l-3.14-3.14ZM12 6.75c3.66 0 6.79 2.12 8.25 5.25a10.96 10.96 0 0 1-1.81 2.6l-1.56-1.56a3.75 3.75 0 0 0-5.92-4.52L9.19 6.75c.87-.24 1.81-.37 2.81-.37Z" />
                        <path d="M12 9a3 3 0 0 1 3 3 .75.75 0 0 1-1.5 0 1.5 1.5 0 0 0-1.5-1.5.75.75 0 0 1 0-1.5Z" />
                    </svg>
                    <svg x-show="showConfirmPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1.44 12.25a.75.75 0 0 1 0-.5C3.3 8.09 7.26 5.25 12 5.25s8.7 2.84 10.56 6.5a.75.75 0 0 1 0 .5C20.7 15.91 16.74 18.75 12 18.75S3.3 15.91 1.44 12.25ZM12 8.25A3.75 3.75 0 1 0 12 15.75 3.75 3.75 0 0 0 12 8.25Zm0 1.5A2.25 2.25 0 1 1 12 14.25 2.25 2.25 0 0 1 12 9.75Z" />
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full rounded-2xl bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                {{ __('Register') }}
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-slate-400">
            <span>{{ __('Already registered?') }}</span>
            <a class="ml-1 font-semibold text-orange-300 transition hover:text-orange-200" href="{{ route('login') }}">
                {{ __('Log in here') }}
            </a>
        </div>
    </form>
</x-guest-layout>
