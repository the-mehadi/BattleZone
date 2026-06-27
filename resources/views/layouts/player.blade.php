<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' | '.config('app.name', 'BattleZone') : config('app.name', 'BattleZone') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(249,115,22,0.18),_transparent_35%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#020617_100%)]">
            <header class="sticky top-0 z-40 border-b border-slate-800/80 bg-slate-950/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 text-lg font-black text-white shadow-lg shadow-orange-500/20">
                            B
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-white">BattleZone</p>
                            <p class="text-xs uppercase tracking-[0.24em] text-orange-400">Free Fire Arena</p>
                        </div>
                    </a>

                    <nav class="hidden items-center gap-2 md:flex">
                        <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }} rounded-xl px-4 py-2 text-sm font-semibold transition">
                            Rooms
                        </a>
                        <a href="{{ route('leaderboard.index') }}" class="{{ request()->routeIs('leaderboard.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }} rounded-xl px-4 py-2 text-sm font-semibold transition">
                            Leaderboard
                        </a>

                        @auth
                            <a href="{{ route('my-squads.index') }}" class="{{ request()->routeIs('my-squads.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }} rounded-xl px-4 py-2 text-sm font-semibold transition">
                                My Squads
                            </a>
                            <a href="{{ route('wallet.index') }}" class="{{ request()->routeIs('wallet.*') ? 'bg-orange-500/15 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }} rounded-xl px-4 py-2 text-sm font-semibold transition">
                                Wallet
                            </a>
                            <a href="{{ route('profile.edit') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-800/80 hover:text-white">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                                    Log Out
                                </button>
                            </form>
                        @endauth

                        @guest
                            <a href="{{ route('login') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-800/80 hover:text-white">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-orange-400">
                                Register
                            </a>
                        @endguest
                    </nav>
                </div>
            </header>

            <main>
                @if (session('success') || session('error'))
                    <div class="mx-auto max-w-7xl px-6 pt-6">
                        @if (session('success'))
                            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-200">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-200">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
