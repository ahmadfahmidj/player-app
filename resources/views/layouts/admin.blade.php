<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Hospital TV</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        /* Custom scrollbar for a premium feel */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>

<body class="bg-slate-900 min-h-screen text-slate-200 antialiased relative selection:bg-blue-500/30">
    <!-- Background glowing orbs -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-blue-600/20 blur-[120px]"></div>
        <div class="absolute top-[20%] -right-[10%] w-[30%] h-[50%] rounded-full bg-indigo-600/10 blur-[100px]"></div>
    </div>

    <!-- Glassmorphic Navbar -->
    <nav
        class="relative z-20 sticky top-0 w-full border-b border-white/10 bg-slate-900/50 backdrop-blur-xl px-6 py-4 flex items-center justify-between shadow-2xl">
        <div class="flex items-center gap-8">
            <span
                class="font-extrabold text-xl text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300 tracking-tight flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                Hospital TV
            </span>
            <div class="flex gap-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">Dashboard</a>
                <a href="{{ route('admin.videos') }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.videos') ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">Videos</a>
                <a href="{{ route('admin.settings') }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.settings') ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">Settings</a>
            </div>
        </div>
        <div class="flex items-center gap-5">
            <a href="{{ route('player') }}" target="_blank"
                class="flex items-center gap-1.5 text-xs font-semibold px-4 py-2 bg-indigo-500/10 text-indigo-300 rounded-full border border-indigo-500/20 hover:bg-indigo-500/20 hover:text-indigo-200 transition-all">
                Open Player
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
            <div class="w-px h-6 bg-slate-700"></div>
            <form method="POST" action="/logout" class="m-0">
                @csrf
                <button type="submit"
                    class="text-sm font-medium text-red-400 hover:text-red-300 transition-colors flex items-center gap-2">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="relative z-10 p-6 md:p-8 max-w-7xl mx-auto space-y-6">
        @if (session('success'))
            <div
                class="animate-in fade-in slide-in-from-top-4 duration-500 mb-6 bg-emerald-500/10 border border-emerald-500/20 shadow-[0_0_20px_rgba(16,185,129,0.15)] text-emerald-400 rounded-xl px-5 py-4 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="animate-in fade-in slide-in-from-top-4 duration-500 mb-6 bg-rose-500/10 border border-rose-500/20 shadow-[0_0_20px_rgba(244,63,94,0.15)] text-rose-400 rounded-xl px-5 py-4 text-sm font-medium flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>

</html>
