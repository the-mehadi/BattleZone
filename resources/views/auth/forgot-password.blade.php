<x-guest-layout>
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Recover Account</p>
        <h2 class="mt-2 text-3xl font-black text-white">{{ __('Forgot Password') }}</h2>
        <p class="mt-3 text-sm leading-6 text-slate-400">
            {{ __('Enter your email address and we will send you a password reset link so you can get back into BattleZone.') }}
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-200">{{ __('Email') }}</label>
            <input
                id="email"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email address"
                required
                autofocus
            >
            @error('email')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full rounded-2xl bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-slate-400">
            <a class="font-semibold text-orange-300 transition hover:text-orange-200" href="{{ route('login') }}">
                {{ __('Back to login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
