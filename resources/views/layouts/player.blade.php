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
            <div class="fixed inset-x-0 top-0 z-40 border-b border-zinc-800/80 bg-zinc-950/95 backdrop-blur lg:hidden">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 text-base font-black text-white shadow-lg shadow-orange-500/20">
                            B
                        </div>
                        <div>
                            <p class="text-base font-semibold text-white">BattleZone</p>
                            <p class="text-[10px] uppercase tracking-[0.22em] text-orange-400">Free Fire Arena</p>
                        </div>
                    </a>
                </div>
            </div>

            <x-bottom-navbar />


            <main class="pb-24 pt-24 lg:pb-8 lg:pt-24">
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
