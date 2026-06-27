<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BattleZone') }} Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 font-sans text-slate-100 antialiased">
        <div class="min-h-screen lg:flex">
            <aside class="w-full border-b border-slate-800 bg-slate-900 lg:min-h-screen lg:w-72 lg:border-b-0 lg:border-r">
                <div class="flex h-full flex-col">
                    <div class="border-b border-slate-800 px-6 py-6">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-500/20 text-lg font-bold text-orange-400">
                                B
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-white">BattleZone</p>
                                <p class="text-sm text-slate-400">Moderator Panel</p>
                            </div>
                        </a>
                    </div>

                    <nav class="flex-1 space-y-2 px-4 py-6">
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-medium transition">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'bg-orange-500/20 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-medium transition">
                            Categories
                        </a>
                        <a href="{{ route('admin.rooms.index') }}" class="{{ request()->routeIs('admin.rooms.*') ? 'bg-orange-500/20 text-orange-300 ring-1 ring-orange-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-medium transition">
                            Rooms
                        </a>
                        <a href="#" class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-800/40 hover:text-slate-300">
                            Payments
                        </a>
                        <a href="#" class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-800/40 hover:text-slate-300">
                            Players
                        </a>
                        <a href="#" class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-800/40 hover:text-slate-300">
                            Settings
                        </a>
                    </nav>

                    <div class="border-t border-slate-800 px-4 py-5">
                        <div class="mb-4 rounded-xl bg-slate-800/80 px-4 py-3">
                            <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs uppercase tracking-wide text-slate-400">{{ auth()->user()->role }}</p>
                        </div>

                        <div class="space-y-2">
                            <a href="{{ route('profile.edit') }}" class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-slate-800 hover:text-white">
                                Profile
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-xl bg-slate-800 px-4 py-3 text-left text-sm font-medium text-slate-200 transition hover:bg-slate-700 hover:text-white">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex-1">
                <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur">
                    <div class="flex items-center justify-between px-6 py-5 lg:px-10">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.2em] text-orange-400">BattleZone</p>
                            <h1 class="mt-1 text-2xl font-semibold text-white">{{ $title ?? 'Moderator Panel' }}</h1>
                        </div>

                        <div class="text-right">
                            <p class="text-sm text-slate-400">Manage tournaments, rooms, and rewards</p>
                        </div>
                    </div>
                </header>

                <main class="px-6 py-8 lg:px-10">
                    @if (session('success'))
                        <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
