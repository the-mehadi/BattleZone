<section class="space-y-6">
    <header>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-400">Danger Zone</p>
        <h2 class="mt-2 text-2xl font-bold text-white">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-sm text-slate-400">
            {{ __('Deleting your account is permanent. Your profile, squads, and related data will be removed from BattleZone.') }}
        </p>
    </header>

    <div class="rounded-3xl border border-rose-500/20 bg-rose-500/10 p-5">
        <p class="text-sm leading-6 text-rose-100">
            {{ __('Enter your current password to confirm account deletion. This action cannot be undone.') }}
        </p>
    </div>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5">
        @csrf
        @method('delete')

        <div>
            <label for="delete_account_password" class="block text-sm font-semibold text-slate-200">{{ __('Current Password') }}</label>
            <input
                id="delete_account_password"
                name="password"
                type="password"
                placeholder="{{ __('Enter your password') }}"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/30"
            >
            @if ($errors->userDeletion->has('password'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->userDeletion->first('password') }}</p>
            @endif
        </div>

        <button type="submit" class="rounded-2xl bg-rose-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-rose-400">
            {{ __('Delete Account Permanently') }}
        </button>
    </form>
</section>
