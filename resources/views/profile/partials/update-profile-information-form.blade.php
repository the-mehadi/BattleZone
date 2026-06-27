<section>
    <header>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Profile</p>
        <h2 class="mt-2 text-2xl font-bold text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-2 text-sm text-slate-400">
            {{ __("Update your BattleZone display information and keep your email address current.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-200">{{ __('Name') }}</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
                >
                @error('name')
                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-slate-200">Phone</label>
                <input
                    id="phone"
                    type="text"
                    value="{{ $user->phone }}"
                    disabled
                    class="mt-2 block w-full rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3 text-slate-400"
                >
                <p class="mt-2 text-xs text-slate-500">Phone login is managed by the system and cannot be changed here.</p>
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-200">{{ __('Email') }}</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="mt-2 block w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40"
            >
            @error('email')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-4">
                    <p class="text-sm text-amber-100">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="ml-1 font-semibold text-orange-300 underline transition hover:text-orange-200 focus:outline-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-300">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="rounded-2xl bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-300"
                >{{ __('Profile updated successfully.') }}</p>
            @endif
        </div>
    </form>
</section>
