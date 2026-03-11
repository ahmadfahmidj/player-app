@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-6xl mx-auto space-y-4">

        <div
            class="flex flex-col md:flex-row justify-between items-center bg-[#f0f0f0] border border-gray-400 px-4 py-3 rounded shadow shadow-gray-400/20">
            <div>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-orange-500 hover:scale-110 transition-transform" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path d="M12 2L2 22H22L12 2Z" />
                    </svg>
                    Remote Access / Playback Network
                </h1>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Left Column: Status & Controls --}}
            <div class="lg:col-span-7 space-y-4">

                {{-- Current Status Board --}}
                <div class="bg-gray-100 border border-gray-400 p-4 shadow-md rounded">
                    <h2
                        class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-3 flex items-center gap-2 border-b border-gray-300 pb-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Connection Status
                    </h2>

                    <div class="flex items-center gap-4 bg-white rounded border border-gray-300 shadow-inner p-3">
                        <div class="relative flex h-5 w-5">
                            <span id="status-ping"
                                class="absolute inline-flex h-full w-full rounded-full opacity-75 {{ $state->is_playing ? 'animate-ping bg-green-400' : 'hidden' }}"></span>
                            <span id="status-indicator"
                                class="relative inline-flex rounded-full h-5 w-5 {{ $state->is_playing ? 'bg-green-500 border border-green-700 shadow-sm' : 'bg-gray-400 border border-gray-500 inset-shadow-sm' }}"></span>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-baseline gap-3 pb-1 mb-1 border-b border-gray-100">
                                <span id="status-text"
                                    class="text-sm font-bold {{ $state->is_playing ? 'text-green-600' : 'text-gray-500' }} uppercase tracking-wider">
                                    {{ $state->is_playing ? '▶ Playing' : '■ Paused' }}
                                </span>
                                <span id="current-video-title" class="text-gray-800 font-medium truncate text-sm">
                                    {{ $state->video?->title ?? 'No video selected' }}
                                </span>
                            </div>
                            <div
                                class="flex items-center gap-2 text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                                State: <span id="current-loop"
                                    class="text-blue-600 bg-blue-50 border border-blue-200 px-1 rounded">{{ $state->loop_mode }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Playback Controls Board --}}
                <div class="bg-[#f0f0f0] border border-gray-400 shadow-md rounded flex flex-col items-center p-4">
                    <h2
                        class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2 self-start w-full border-b border-gray-300 pb-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                        Control Interface
                    </h2>

                    <!-- VLC style big buttons -->
                    <div class="flex gap-2 w-full justify-center mb-6">
                        <button onclick="adminPlay()"
                            class="px-8 py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 text-gray-800 rounded shadow hover:shadow-md font-bold uppercase text-xs flex items-center gap-2 active:bg-gray-400 active:shadow-none transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg> Play/Resume
                        </button>
                        <button onclick="adminPause()"
                            class="px-8 py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 text-gray-800 rounded shadow hover:shadow-md font-bold uppercase text-xs flex items-center gap-2 active:bg-gray-400 active:shadow-none transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                            </svg> Pause/Halt
                        </button>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full bg-white p-3 border border-gray-300 rounded shadow-inner">
                        {{-- Seek --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-600 uppercase tracking-wider block">Seek to
                                (seconds)</label>
                            <div class="flex items-center gap-1">
                                <input type="number" id="seek-input" min="0" step="1" value="0"
                                    class="bg-white border border-gray-400 text-gray-800 rounded px-2 py-1 text-sm w-full focus:outline-none focus:border-orange-500 shadow-inner">
                                <button onclick="adminSeek()"
                                    class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 border border-gray-400 text-gray-800 rounded font-bold shadow-sm transition-all hover:-translate-y-px active:translate-y-px">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Loop Mode --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-600 uppercase tracking-wider block">Loop
                                Mode</label>
                            <div class="relative">
                                <select id="loop-select" onchange="adminLoop(this.value)"
                                    class="w-full bg-white border border-gray-400 text-gray-800 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-orange-500 shadow-inner appearance-none cursor-pointer">
                                    <option value="none" {{ $state->loop_mode === 'none' ? 'selected' : '' }}>No Loop
                                    </option>
                                    <option value="single" {{ $state->loop_mode === 'single' ? 'selected' : '' }}>Loop
                                        Single Video</option>
                                    <option value="playlist" {{ $state->loop_mode === 'playlist' ? 'selected' : '' }}>Loop
                                        Entire Playlist</option>
                                </select>
                                <svg class="w-4 h-4 absolute right-2 top-[6px] pointer-events-none text-gray-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column: Playlist --}}
            <div class="lg:col-span-5 relative">
                <div
                    class="sticky top-20 rounded bg-[#f0f0f0] border border-gray-400 shadow-md flex flex-col max-h-[calc(100vh-8rem)] overflow-hidden">
                    <div
                        class="p-3 bg-gray-200 border-b border-gray-400 shrink-0 flex items-center justify-between shadow-sm">
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Network Queue
                        </h2>
                        <span
                            class="bg-gray-100 border border-gray-300 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded shadow-inner">{{ $videos->count() }}
                            media file(s)</span>
                    </div>

                    <div
                        class="overflow-y-auto p-1 bg-white space-y-px flex-grow custom-scrollbar border-b border-gray-300">
                        @if ($videos->isEmpty())
                            <div class="flex flex-col items-center justify-center p-8 text-center h-full">
                                <svg class="w-8 h-8 text-gray-300 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                                </svg>
                                <p class="text-gray-400 text-xs mb-3 italic">Playlist empty.</p>
                                <a href="{{ route('admin.videos') }}"
                                    class="px-4 py-1.5 bg-gray-100 border border-gray-300 hover:border-gray-400 text-gray-600 rounded text-xs font-bold shadow-sm transition-all hover:bg-gray-200">Load
                                    Media...</a>
                            </div>
                        @else
                            @foreach ($videos as $video)
                                <div
                                    class="group flex items-center gap-3 px-3 py-2 cursor-default {{ $state->current_video_id === $video->id ? 'bg-orange-50 border-l-4 border-orange-500' : 'hover:bg-blue-50 border-l-4 border-transparent' }}">
                                    <!-- Indicator -->
                                    <div class="w-4 shrink-0 text-center text-xs font-bold font-mono text-gray-500">
                                        @if ($state->current_video_id === $video->id)
                                            <span class="text-orange-500 animate-pulse">▶</span>
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-grow">
                                        <h3
                                            class="text-xs font-bold truncate {{ $state->current_video_id === $video->id ? 'text-gray-900' : 'text-gray-700' }}">
                                            {{ $video->title }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span
                                                class="text-[10px] text-gray-500 font-mono">{{ gmdate('m:s', $video->duration) }}</span>
                                        </div>
                                    </div>

                                    <button onclick="adminChange({{ $video->id }})"
                                        class="shrink-0 opacity-0 group-hover:opacity-100 px-2 py-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-[10px] font-bold uppercase rounded border border-gray-300 transition-all focus:opacity-100">
                                        Cast
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <!-- VLC mock bottom frame -->
                    <div class="h-4 bg-gray-200 border-t border-gray-300 flex items-center px-2">
                        <span class="text-[9px] text-gray-500 uppercase font-mono">Status: Ready</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 12px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F0F0F0;
            border-left: 1px solid #D1D5DB;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border: 2px solid #F0F0F0;
            border-radius: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

                // VLC style flash
                const body = document.querySelector('body');
                const flash = document.createElement('div');
                flash.className =
                    'fixed left-1/2 top-10 -translate-x-1/2 bg-gray-900 text-white font-mono text-sm px-4 py-1 rounded shadow-xl pointer-events-none z-50 transition-opacity duration-300 opacity-90';
                flash.innerHTML = '> Command sent';
                body.appendChild(flash);
                setTimeout(() => {
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 300);
                }, 500);

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
