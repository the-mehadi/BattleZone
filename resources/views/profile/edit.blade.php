<x-player-layout title="Profile">
    <section class="mx-auto max-w-7xl px-6 py-14">
        <div class="mb-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Account Center</p>
                <h1 class="mt-2 text-4xl font-black text-white">Manage Your Profile</h1>
                <p class="mt-3 max-w-2xl text-base text-slate-300">
                    Update your BattleZone account details, change your password, and keep your account secure.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 px-5 py-4 text-sm text-slate-300">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Phone</p>
                    <p class="mt-2 text-base font-semibold text-white">{{ $user->phone }}</p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900/80 px-5 py-4 text-sm text-slate-300">
                    <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Role</p>
                    <p class="mt-2 text-base font-semibold capitalize text-white">{{ $user->role }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30 sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>

                <div class="rounded-[2rem] border border-slate-800 bg-slate-900/80 p-6 shadow-2xl shadow-slate-950/30">
                    <h2 class="text-xl font-bold text-white">Security Tips</h2>
                    <div class="mt-5 space-y-4 text-sm text-slate-300">
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-4">
                            Use a strong password with letters, numbers, and symbols.
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-4">
                            Keep your email address accessible for account recovery.
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-4">
                            Do not share your login details with teammates or moderators.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-player-layout>
