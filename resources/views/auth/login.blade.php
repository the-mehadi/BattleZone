<x-guest-layout>
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Welcome Back</p>
        <h2 class="mt-2 text-3xl font-black text-white">{{ __('Log In To BattleZone') }}</h2>
        <p class="mt-3 text-sm leading-6 text-slate-400">
            Sign in with your phone number and password to join rooms, manage squads, and track your tournament progress.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="phone" class="block text-sm font-semibold text-slate-200">{{ __('Phone') }}</label>
            <input
                id="phone"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                type="text"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="Enter your phone number"
                required
                autofocus
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
                placeholder="Enter your password"
                required
                autocomplete="current-password"
            >
            @error('password')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center gap-3">
                <input id="remember_me" type="checkbox" class="rounded border-slate-700 bg-slate-950 text-orange-500 shadow-sm focus:ring-orange-500" name="remember">
                <span class="text-sm text-slate-300">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-orange-300 transition hover:text-orange-200" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full rounded-2xl bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                {{ __('Log in') }}
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-slate-400">
            <span>{{ __('New to BattleZone?') }}</span>
            <a class="ml-1 font-semibold text-orange-300 transition hover:text-orange-200" href="{{ route('register') }}">
                {{ __('Create an account') }}
            </a>
        </div>
    </form>
</x-guest-layout>
