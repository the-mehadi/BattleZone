<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BattleZone') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(249,115,22,0.18),_transparent_35%),linear-gradient(180deg,_#020617_0%,_#0f172a_45%,_#020617_100%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl items-center px-6 py-10">
                <div class="grid w-full gap-8 lg:grid-cols-[0.95fr_1.05fr]">
                    <div class="hidden rounded-[2rem] border border-slate-800 bg-slate-900/60 p-10 shadow-2xl shadow-slate-950/30 lg:flex lg:flex-col lg:justify-between">
                        <div>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 text-xl font-black text-white shadow-lg shadow-orange-500/20">
                                    B
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-white">BattleZone</p>
                                    <p class="text-xs uppercase tracking-[0.26em] text-orange-400">Free Fire Arena</p>
                                </div>
                            </a>

                            <div class="mt-12 space-y-5">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-400">Tournament Access</p>
                                <h1 class="text-5xl font-black leading-tight text-white">
                                    Join rooms, track squads, and compete for real prizes.
                                </h1>
                                <p class="max-w-xl text-base leading-7 text-slate-300">
                                    Manage your BattleZone account with a faster, cleaner auth experience built for tournament players and moderators.
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Fast Join</p>
                                <p class="mt-2 text-sm font-semibold text-white">Phone-first access</p>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Secure</p>
                                <p class="mt-2 text-sm font-semibold text-white">Protected account flow</p>
                            </div>
                            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Competitive</p>
                                <p class="mt-2 text-sm font-semibold text-white">Prize-ready platform</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center">
                        <div class="w-full max-w-xl rounded-[2rem] border border-slate-800 bg-slate-900/85 p-6 shadow-2xl shadow-slate-950/40 backdrop-blur sm:p-8">
                            <div class="mb-8 lg:hidden">
                                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 text-lg font-black text-white shadow-lg shadow-orange-500/20">
                                        B
                                    </div>
                                    <div>
                                        <p class="text-xl font-bold text-white">BattleZone</p>
                                        <p class="text-xs uppercase tracking-[0.24em] text-orange-400">Free Fire Arena</p>
                                    </div>
                                </a>
                            </div>

                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
