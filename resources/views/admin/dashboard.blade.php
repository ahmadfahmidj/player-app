@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8 animate-in fade-in zoom-in-95 duration-500">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1
                    class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300 tracking-tight">
                    Playback Control</h1>
                <p class="text-slate-400 mt-1">Manage what's playing on Hospital TV in real-time</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Left Column: Status & Controls --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- Current Status Board --}}
                <div
                    class="relative overflow-hidden rounded-2xl bg-slate-800/50 backdrop-blur-xl border border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-6 group">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Current Status
                    </h2>

                    <div class="flex items-center gap-5 bg-slate-900/50 rounded-xl p-4 border border-white/5 shadow-inner">
                        <div class="relative flex h-4 w-4">
                            <span id="status-ping"
                                class="absolute inline-flex h-full w-full rounded-full opacity-75 {{ $state->is_playing ? 'animate-ping bg-emerald-400' : 'hidden' }}"></span>
                            <span id="status-indicator"
                                class="relative inline-flex rounded-full h-4 w-4 {{ $state->is_playing ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.8)]' : 'bg-slate-600' }}"></span>
                        </div>
                        <div>
                            <div class="flex items-baseline gap-3">
                                <span id="status-text"
                                    class="text-lg font-bold {{ $state->is_playing ? 'text-emerald-400' : 'text-slate-400' }}">
                                    {{ $state->is_playing ? 'Playing' : 'Paused' }}
                                </span>
                                <span id="current-video-title" class="text-slate-200 font-medium truncate max-w-sm">
                                    {{ $state->video?->title ?? 'No video selected' }}
                                </span>
                            </div>
                            <div
                                class="mt-1 flex items-center gap-2 text-xs text-slate-500 font-medium uppercase tracking-wider">
                                Loop Mode: <span id="current-loop" class="text-indigo-400">{{ $state->loop_mode }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Playback Controls Board --}}
                <div
                    class="rounded-2xl bg-slate-800/50 backdrop-blur-xl border border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-6">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                        Master Controls
                    </h2>

                    <div class="flex flex-wrap gap-4 mb-8">
                        <button onclick="adminPlay()"
                            class="relative overflow-hidden group px-8 py-3 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 hover:border-emerald-500 text-emerald-400 rounded-xl font-bold text-sm transition-all duration-300 shadow-[0_0_15px_rgba(16,185,129,0.1)] hover:shadow-[0_0_25px_rgba(16,185,129,0.3)] flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            Play
                        </button>
                        <button onclick="adminPause()"
                            class="relative overflow-hidden group px-8 py-3 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 hover:border-amber-500 text-amber-500 rounded-xl font-bold text-sm transition-all duration-300 shadow-[0_0_15px_rgba(245,158,11,0.1)] hover:shadow-[0_0_25px_rgba(245,158,11,0.3)] flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                            </svg>
                            Pause
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Seek --}}
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Seek to
                                (seconds)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="seek-input" min="0" step="1" value="0"
                                    class="bg-slate-900/50 border border-slate-700 text-slate-200 rounded-lg px-4 py-2.5 text-sm w-full focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono transition-colors">
                                <button onclick="adminSeek()"
                                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Loop Mode --}}
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Loop
                                Mode</label>
                            <select id="loop-select" onchange="adminLoop(this.value)"
                                class="bg-slate-900/50 border border-slate-700 text-slate-200 rounded-lg px-4 py-2.5 text-sm w-full focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-medium transition-colors appearance-none">
                                <option value="none" {{ $state->loop_mode === 'none' ? 'selected' : '' }}>No Loop</option>
                                <option value="single" {{ $state->loop_mode === 'single' ? 'selected' : '' }}>Loop Single
                                    Video</option>
                                <option value="playlist" {{ $state->loop_mode === 'playlist' ? 'selected' : '' }}>Loop
                                    Entire Playlist</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column: Playlist --}}
            <div class="lg:col-span-5 relative">
                <div
                    class="sticky top-24 rounded-2xl bg-slate-800/50 backdrop-blur-xl border border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-0 flex flex-col max-h-[calc(100vh-8rem)]">
                    <div class="p-5 border-b border-white/5 shrink-0 flex items-center justify-between">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Up Next
                        </h2>
                        <span
                            class="bg-indigo-500/20 text-indigo-300 text-xs font-bold px-2.5 py-1 rounded-md">{{ $videos->count() }}
                            Videos</span>
                    </div>

                    <div class="overflow-y-auto p-3 space-y-2 flex-grow custom-scrollbar">
                        @if ($videos->isEmpty())
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div
                                    class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mb-4 border border-white/5">
                                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-slate-400 text-sm mb-4">No videos in the queue.</p>
                                <a href="{{ route('admin.videos') }}"
                                    class="px-5 py-2 block bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/20 transition-all">Upload
                                    Videos</a>
                            </div>
                        @else
                            @foreach ($videos as $video)
                                <div
                                    class="group flex items-center gap-4 p-3 rounded-xl border transition-all duration-300 {{ $state->current_video_id === $video->id ? 'border-blue-500/50 bg-blue-500/10 shadow-[0_0_15px_rgba(59,130,246,0.1)]' : 'border-transparent hover:border-white/10 hover:bg-white/5' }}">
                                    <div
                                        class="shrink-0 w-8 text-center text-xs font-black {{ $state->current_video_id === $video->id ? 'text-blue-400' : 'text-slate-600 group-hover:text-slate-400' }}">
                                        @if ($state->current_video_id === $video->id)
                                            <div class="animate-pulse">▶</div>
                                        @else
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-grow">
                                        <h3
                                            class="text-sm font-bold truncate {{ $state->current_video_id === $video->id ? 'text-blue-300' : 'text-slate-200' }}">
                                            {{ $video->title }}</h3>
                                        <p class="text-xs text-slate-500 font-mono mt-0.5">
                                            {{ gmdate('H:i:s', $video->duration) }}</p>
                                    </div>

                                    <button onclick="adminChange({{ $video->id }})"
                                        class="shrink-0 opacity-0 group-hover:opacity-100 px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold rounded-lg transition-all border border-slate-600 hover:border-slate-500 focus:opacity-100">
                                        Play
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>

    <script>
        const CSRF = document.querySelector('meta[name=csrf-token]').content;

        async function post(url, data = {}) {
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify(data)
                });

                // Add subtle click feedback
                const body = document.querySelector('body');
                const flash = document.createElement('div');
                flash.className =
                'fixed inset-0 bg-blue-500/5 pointer-events-none z-50 transition-opacity duration-300';
                body.appendChild(flash);
                setTimeout(() => {
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 300);
                }, 50);

                return res.json();
            } catch (e) {
                console.error(e);
            }
        }

        function adminPlay() {
            post('{{ route('admin.playback.play') }}');
        }

        function adminPause() {
            post('{{ route('admin.playback.pause') }}');
        }

        function adminSeek() {
            const pos = parseFloat(document.getElementById('seek-input').value) || 0;
            post('{{ route('admin.playback.seek') }}', {
                position: pos
            });
        }

        function adminChange(videoId) {
            post('{{ route('admin.playback.change') }}', {
                video_id: videoId
            });
        }

        function adminLoop(mode) {
            post('{{ route('admin.playback.loop') }}', {
                loop_mode: mode
            });
        }
    </script>
@endsection
