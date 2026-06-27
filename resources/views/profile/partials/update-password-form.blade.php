<section>
    <header>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Security</p>
        <h2 class="mt-2 text-2xl font-bold text-white">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-2 text-sm text-slate-400">
            {{ __('Use a strong password to keep your tournament account secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-slate-200">{{ __('Current Password') }}</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
            >
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-slate-200">{{ __('New Password') }}</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
            >
            @if ($errors->updatePassword->has('password'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-slate-200">{{ __('Confirm Password') }}</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
            >
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-300"
                >{{ __('Password updated successfully.') }}</p>
            @endif
        </div>
    </form>
</section>
