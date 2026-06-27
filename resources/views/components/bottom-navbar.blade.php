<nav
    x-data="{
        isVisible: true,
        menuOpen: false,
        lastScrollY: window.scrollY,
        ticking: false,
        hideAfter: 80,
        init() {
            this.onScroll = () => {
                if (this.ticking) {
                    return;
                }

                this.ticking = true;

                window.requestAnimationFrame(() => {
                    const currentScrollY = window.scrollY;
                    const scrollDelta = currentScrollY - this.lastScrollY;

                    if (currentScrollY <= this.hideAfter) {
                        this.isVisible = true;
                    } else if (scrollDelta < -6) {
                        this.isVisible = true;
                    } else if (scrollDelta > 6) {
                        this.isVisible = false;
                        this.menuOpen = false;
                    }

                    this.lastScrollY = currentScrollY;
                    this.ticking = false;
                });
            };

            this.onResize = () => {
                if (window.scrollY <= this.hideAfter) {
                    this.isVisible = true;
                }

                if (window.innerWidth >= 1024) {
                    this.menuOpen = false;
                }
            };

            window.addEventListener('scroll', this.onScroll, { passive: true });
            window.addEventListener('resize', this.onResize, { passive: true });
        },
        destroy() {
            window.removeEventListener('scroll', this.onScroll);
            window.removeEventListener('resize', this.onResize);
        }
    }"
    @click.outside="menuOpen = false"
    @keydown.escape.window="menuOpen = false"
    :class="isVisible ? 'translate-y-0' : 'translate-y-full lg:-translate-y-full'"
    class="fixed inset-x-0 bottom-0 z-50 transform transition-transform duration-300 ease-out lg:top-0 lg:bottom-auto"
>
    <div class="border-t border-zinc-800/90 bg-zinc-950/95 shadow-[0_-12px_30px_rgba(0,0,0,0.35)] backdrop-blur lg:border-t-0 lg:border-b lg:shadow-[0_12px_30px_rgba(0,0,0,0.25)]">
        <div class="mx-auto hidden max-w-7xl items-center justify-between px-6 py-4 lg:flex">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 text-lg font-black text-white shadow-lg shadow-orange-500/20">
                    B
                </div>
                <div>
                    <p class="text-lg font-semibold text-white">BattleZone</p>
                    <p class="text-xs uppercase tracking-[0.24em] text-orange-400">Free Fire Arena</p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3.75a.75.75 0 0 1 .53.22l7.5 7.5a.75.75 0 0 1-1.06 1.06l-.97-.97V19.5A1.5 1.5 0 0 1 16.5 21h-3.75v-4.5h-1.5V21H7.5A1.5 1.5 0 0 1 6 19.5v-7.94l-.97.97a.75.75 0 0 1-1.06-1.06l7.5-7.5a.75.75 0 0 1 .53-.22Z" />
                    </svg>
                    <span>Home</span>
                </a>

                <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5.25 5.25A2.25 2.25 0 0 1 7.5 3h9a2.25 2.25 0 0 1 2.25 2.25v2.379a3.75 3.75 0 0 0 0 8.742v2.379A2.25 2.25 0 0 1 16.5 21h-9a2.25 2.25 0 0 1-2.25-2.25V16.37a3.75 3.75 0 0 0 0-8.742V5.25Z" />
                    </svg>
                    <span>Matches</span>
                </a>

                <a href="{{ route('leaderboard.index') }}" class="{{ request()->routeIs('leaderboard.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16.5 3.75a.75.75 0 0 1 .75.75v1.068A2.25 2.25 0 0 1 19.5 7.818v1.682a4.5 4.5 0 0 1-3.477 4.38 5.264 5.264 0 0 1-2.273 2.363V18h1.5a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1 0-1.5h1.5v-1.757a5.264 5.264 0 0 1-2.273-2.363A4.5 4.5 0 0 1 5 9.5V7.818a2.25 2.25 0 0 1 2.25-2.25V4.5a.75.75 0 0 1 1.5 0v1.125h6V4.5a.75.75 0 0 1 .75-.75ZM8 7.125h-.75a.75.75 0 0 0-.75.75V9.5a3 3 0 0 0 1.707 2.708A5.297 5.297 0 0 1 8 10.75V7.125Zm8 0v3.625c0 .503-.071.989-.207 1.458A3 3 0 0 0 17.5 9.5V7.875a.75.75 0 0 0-.75-.75H16Z" />
                    </svg>
                    <span>Leaderboard</span>
                </a>

                @auth
                    <a href="{{ route('my-squads.index') }}" class="{{ request()->routeIs('my-squads.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.75 5.25A2.25 2.25 0 0 0 4.5 7.5v9a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H6.75Zm1.5 3a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Zm0 3.75A.75.75 0 0 1 9 11.25h6a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Zm0 3.75A.75.75 0 0 1 9 15h3a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                        </svg>
                        <span>My Squads</span>
                    </a>

                    <a href="{{ route('wallet.index') }}" class="{{ request()->routeIs('wallet.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.25 7.5A2.25 2.25 0 0 1 4.5 5.25h13.879a2.25 2.25 0 0 1 2.121 1.5H6a2.25 2.25 0 0 0 0 4.5h15.75v5.25A2.25 2.25 0 0 1 19.5 18.75h-15A2.25 2.25 0 0 1 2.25 16.5v-9ZM6 8.25a.75.75 0 0 0 0 1.5h15.75v-1.5H6Zm12 6a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z" />
                        </svg>
                        <span>Wallet</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5a18.683 18.683 0 0 1-7.812-1.7.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                        </svg>
                        <span>Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5a18.683 18.683 0 0 1-7.812-1.7.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                        </svg>
                        <span>Login</span>
                    </a>

                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-zinc-900 hover:text-white' }} inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4.5a.75.75 0 0 1 .75.75v6h6a.75.75 0 0 1 0 1.5h-6v6a.75.75 0 0 1-1.5 0v-6h-6a.75.75 0 0 1 0-1.5h6v-6A.75.75 0 0 1 12 4.5Z" />
                        </svg>
                        <span>Register</span>
                    </a>
                @endauth
            </div>
        </div>

        <div class="lg:hidden">
            <div
                x-show="menuOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-y-4 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-4 opacity-0"
                class="mx-3 mb-2 rounded-3xl border border-zinc-800 bg-zinc-900/95 p-3 shadow-2xl shadow-black/40"
                style="display: none;"
            >
                @auth
                    <a href="{{ route('my-squads.index') }}" class="{{ request()->routeIs('my-squads.*') ? 'bg-orange-500/15 text-orange-300' : 'text-slate-200 hover:bg-zinc-800 hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.75 5.25A2.25 2.25 0 0 0 4.5 7.5v9a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H6.75Zm1.5 3a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Zm0 3.75A.75.75 0 0 1 9 11.25h6a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Zm0 3.75A.75.75 0 0 1 9 15h3a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                        </svg>
                        <span>My Squads</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'bg-orange-500/15 text-orange-300' : 'text-slate-200 hover:bg-zinc-800 hover:text-white' }} mt-2 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5a18.683 18.683 0 0 1-7.812-1.7.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                        </svg>
                        <span>Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-rose-300 transition hover:bg-zinc-800 hover:text-rose-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.5 3.75A2.25 2.25 0 0 0 5.25 6v12a2.25 2.25 0 0 0 2.25 2.25h3a.75.75 0 0 0 0-1.5h-3A.75.75 0 0 1 6.75 18V6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 0 0-1.5h-3ZM16.72 7.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1 0 1.06l-3.75 3.75a.75.75 0 1 1-1.06-1.06l2.47-2.47H9.75a.75.75 0 0 1 0-1.5h9.44l-2.47-2.47a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                            <span>Log Out</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'bg-orange-500/15 text-orange-300' : 'text-slate-200 hover:bg-zinc-800 hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4.5a.75.75 0 0 1 .75.75v6h6a.75.75 0 0 1 0 1.5h-6v6a.75.75 0 0 1-1.5 0v-6h-6a.75.75 0 0 1 0-1.5h6v-6A.75.75 0 0 1 12 4.5Z" />
                        </svg>
                        <span>Register</span>
                    </a>
                @endauth
            </div>

            <div class="grid grid-cols-5 gap-1 px-2 py-2" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-orange-500' : 'text-slate-400' }} flex flex-col items-center justify-center rounded-2xl px-2 py-2 text-[11px] font-semibold transition hover:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3.75a.75.75 0 0 1 .53.22l7.5 7.5a.75.75 0 0 1-1.06 1.06l-.97-.97V19.5A1.5 1.5 0 0 1 16.5 21h-3.75v-4.5h-1.5V21H7.5A1.5 1.5 0 0 1 6 19.5v-7.94l-.97.97a.75.75 0 0 1-1.06-1.06l7.5-7.5a.75.75 0 0 1 .53-.22Z" />
                    </svg>
                    <span class="mt-1">Home</span>
                </a>

                <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'text-orange-500' : 'text-slate-400' }} flex flex-col items-center justify-center rounded-2xl px-2 py-2 text-[11px] font-semibold transition hover:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5.25 5.25A2.25 2.25 0 0 1 7.5 3h9a2.25 2.25 0 0 1 2.25 2.25v2.379a3.75 3.75 0 0 0 0 8.742v2.379A2.25 2.25 0 0 1 16.5 21h-9a2.25 2.25 0 0 1-2.25-2.25V16.37a3.75 3.75 0 0 0 0-8.742V5.25Z" />
                    </svg>
                    <span class="mt-1">Matches</span>
                </a>

                <a href="{{ route('leaderboard.index') }}" class="{{ request()->routeIs('leaderboard.*') ? 'text-orange-500' : 'text-slate-400' }} flex flex-col items-center justify-center rounded-2xl px-2 py-2 text-[11px] font-semibold transition hover:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16.5 3.75a.75.75 0 0 1 .75.75v1.068A2.25 2.25 0 0 1 19.5 7.818v1.682a4.5 4.5 0 0 1-3.477 4.38 5.264 5.264 0 0 1-2.273 2.363V18h1.5a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1 0-1.5h1.5v-1.757a5.264 5.264 0 0 1-2.273-2.363A4.5 4.5 0 0 1 5 9.5V7.818a2.25 2.25 0 0 1 2.25-2.25V4.5a.75.75 0 0 1 1.5 0v1.125h6V4.5a.75.75 0 0 1 .75-.75ZM8 7.125h-.75a.75.75 0 0 0-.75.75V9.5a3 3 0 0 0 1.707 2.708A5.297 5.297 0 0 1 8 10.75V7.125Zm8 0v3.625c0 .503-.071.989-.207 1.458A3 3 0 0 0 17.5 9.5V7.875a.75.75 0 0 0-.75-.75H16Z" />
                    </svg>
                    <span class="mt-1">Leaderboard</span>
                </a>

                @auth
                    <a href="{{ route('wallet.index') }}" class="{{ request()->routeIs('wallet.*') ? 'text-orange-500' : 'text-slate-400' }} flex flex-col items-center justify-center rounded-2xl px-2 py-2 text-[11px] font-semibold transition hover:text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.25 7.5A2.25 2.25 0 0 1 4.5 5.25h13.879a2.25 2.25 0 0 1 2.121 1.5H6a2.25 2.25 0 0 0 0 4.5h15.75v5.25A2.25 2.25 0 0 1 19.5 18.75h-15A2.25 2.25 0 0 1 2.25 16.5v-9ZM6 8.25a.75.75 0 0 0 0 1.5h15.75v-1.5H6Zm12 6a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z" />
                        </svg>
                        <span class="mt-1">Wallet</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'text-orange-500' : 'text-slate-400' }} flex flex-col items-center justify-center rounded-2xl px-2 py-2 text-[11px] font-semibold transition hover:text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5a18.683 18.683 0 0 1-7.812-1.7.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                        </svg>
                        <span class="mt-1">Login</span>
                    </a>
                @endauth

                <button
                    type="button"
                    @click="menuOpen = !menuOpen"
                    class="flex flex-col items-center justify-center rounded-2xl px-2 py-2 text-[11px] font-semibold transition"
                    :class="menuOpen || {{ request()->routeIs('my-squads.*') || request()->routeIs('profile.*') || request()->routeIs('register') ? 'true' : 'false' }} ? 'text-orange-500' : 'text-slate-400 hover:text-orange-400'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.75 6.75A.75.75 0 0 1 4.5 6h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Zm0 5.25a.75.75 0 0 1 .75-.75h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Zm0 5.25a.75.75 0 0 1 .75-.75h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Z" />
                    </svg>
                    <span class="mt-1">More</span>
                </button>
            </div>
        </div>
    </div>
</nav>
