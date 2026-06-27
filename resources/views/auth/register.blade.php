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

        <div class="mt-4">
            <label for="password" class="block text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
            <input
                id="password"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                type="password"
                name="password"
                placeholder="Create a strong password"
                required
                autocomplete="new-password"
            >
            @error('password')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-200">{{ __('Confirm Password') }}</label>
            <input
                id="password_confirmation"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                type="password"
                name="password_confirmation"
                placeholder="Confirm your password"
                required
                autocomplete="new-password"
            >
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
