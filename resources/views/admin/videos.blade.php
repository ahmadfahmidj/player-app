@extends('layouts.admin')
@section('title', 'Video Player Library')

@section('content')
    <div class="space-y-6 animate-in fade-in zoom-in-95 duration-500">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1
                    class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300 tracking-tight">
                    Studio Player</h1>
                <p class="text-slate-400 mt-1">Manage, preview, and organize your broadcast lineup</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Player & Meta --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Modern Player Container --}}
                <div
                    class="relative group rounded-2xl overflow-hidden bg-black shadow-[0_0_50px_rgba(0,0,0,0.6)] border border-slate-800/80 aspect-video ring-1 ring-white/10">
                    @if ($videos->isNotEmpty())
                        <video id="preview-player" class="w-full h-full object-contain bg-black" controls>
                            <source id="preview-source" src="{{ asset('storage/' . $videos->first()->path) }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <div
                            class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 border border-slate-800 text-center p-6">
                            <svg class="w-16 h-16 text-slate-700 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-slate-500 font-medium block">No videos available to preview</span>
                            <span class="text-slate-600 text-sm mt-1">Upload a video to see it here</span>
                        </div>
                    @endif
                </div>

                {{-- Now Playing Meta --}}
                <div
                    class="bg-slate-800/40 backdrop-blur-xl border border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-32 bg-blue-500/5 blur-[100px] pointer-events-none"></div>

                    <h2 id="now-playing-title" class="text-2xl font-bold tracking-tight text-white mb-2">
                        {{ $videos->isNotEmpty() ? $videos->first()->title : 'No Video Selected' }}
                    </h2>

                    <div class="flex flex-wrap items-center gap-4 text-sm font-mono text-slate-400">
                        <span id="now-playing-filename"
                            class="flex items-center gap-1.5 bg-slate-900/50 px-3 py-1.5 rounded-lg border border-slate-700">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            {{ $videos->isNotEmpty() ? $videos->first()->filename : '--' }}
                        </span>
                        <span
                            class="flex items-center gap-1.5 bg-slate-900/50 px-3 py-1.5 rounded-lg border border-slate-700">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Admin Preview Player
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right Column: Upload & Playlist --}}
            <div class="space-y-6">
                {{-- Compact Upload Form --}}
                <div
                    class="bg-indigo-900/20 border border-indigo-500/30 backdrop-blur-md rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div
                        class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none">
                    </div>
                    <h3 class="text-sm font-bold text-indigo-300 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Quick Upload
                    </h3>

                    <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4 relative z-10 w-full">
                        @csrf
                        <div>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                placeholder="Video Title..."
                                class="bg-slate-900/80 border border-indigo-500/30 text-slate-200 rounded-xl px-4 py-2 text-sm w-full focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition-colors">
                            @error('title')
                                <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative group/file">
                            <input type="file" name="video" accept="video/mp4" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div
                                class="bg-slate-900/80 border border-dashed border-indigo-500/30 group-hover/file:border-indigo-400 rounded-xl px-3 py-4 text-center transition-colors flex flex-col items-center">
                                <svg class="w-6 h-6 text-indigo-400/70 mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium text-indigo-200">Select MP4 File</span>
                            </div>
                        </div>
                        @error('video')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl py-2.5 font-bold text-sm transition-all duration-300 shadow-[0_0_15px_rgba(79,70,229,0.3)] hover:shadow-[0_0_25px_rgba(79,70,229,0.5)]">
                            Upload to Queue
                        </button>
                    </form>
                </div>

                {{-- Playlist Queue --}}
                <div
                    class="bg-slate-800/50 backdrop-blur-xl border border-white/10 rounded-2xl flex flex-col h-[500px] shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
                    <div class="p-5 border-b border-white/10 shrink-0 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-300 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            Up Next Queue
                        </h3>
                        <span
                            class="bg-slate-900 border border-slate-700 text-slate-400 text-xs font-bold px-2 py-1 rounded-md">
                            Drag to reorder
                        </span>
                    </div>

                    @if ($videos->isEmpty())
                        <div class="flex-1 flex flex-col items-center justify-center p-6 text-center">
                            <div
                                class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center mb-3 border border-white/5">
                                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-xs">Queue is empty</p>
                        </div>
                    @else
                        <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar" id="video-list">
                            @foreach ($videos as $index => $video)
                                <div class="group relative flex flex-col gap-2 p-3 rounded-xl border border-transparent hover:border-white/10 bg-slate-900/40 hover:bg-slate-800 transition-all duration-300 cursor-move"
                                    data-id="{{ $video->id }}">

                                    {{-- Playback Interaction Area --}}
                                    <div class="flex items-start gap-3 w-full"
                                        onclick="playVideo('{{ asset('storage/' . $video->path) }}', '{{ addslashes($video->title) }}', '{{ addslashes($video->filename) }}')">
                                        <div class="shrink-0 w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-slate-500 group-hover:bg-blue-600/20 group-hover:text-blue-400 transition-colors cursor-pointer"
                                            title="Click to preview">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-grow cursor-pointer custom-click-area">
                                            <h4
                                                class="text-sm font-bold text-slate-200 truncate group-hover:text-blue-400 transition-colors">
                                                {{ $video->title }}</h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-[10px] font-mono text-slate-500 bg-slate-900 px-1.5 py-0.5 rounded border border-slate-800">{{ gmdate('H:i:s', $video->duration) }}</span>
                                                <span
                                                    class="text-[10px] text-slate-600 truncate">{{ $video->filename }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div
                                        class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST"
                                            onsubmit="return confirm('Remove {{ addslashes($video->title) }} from queue?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white rounded-md transition-colors"
                                                title="Remove from queue">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Scrollbar for Playlist */
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

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }

        [draggable=true] {
            cursor: move;
        }

        .dragging {
            opacity: 0.5;
            border-color: rgb(59 130 246 / 0.5) !important;
            background: rgb(59 130 246 / 0.1) !important;
            transform: scale(0.98);
        }

        /* Play button animate on hover */
        .group:hover .custom-click-area h4 {
            transform: translateX(2px);
        }
    </style>

    <script>
        function playVideo(url, title, filename) {
            const player = document.getElementById('preview-player');
            if (!player) return;
            const source = document.getElementById('preview-source');

            source.src = url;
            player.load();
            player.play().catch(e => console.log('Autoplay prevented by browser'));

            document.getElementById('now-playing-title').innerText = title;
            document.getElementById('now-playing-filename').innerText = filename;
        }

        // Drag to reorder
        (function() {
            const list = document.getElementById('video-list');
            if (!list) return;
            let dragging = null;

            list.querySelectorAll('[data-id]').forEach(item => {
                item.setAttribute('draggable', true);

                item.addEventListener('dragstart', (e) => {
                    // Prevent drag if clicking on the play button or delete button
                    if (e.target.tagName.toLowerCase() === 'button' || e.target.closest('button')) {
                        e.preventDefault();
                        return;
                    }
                    dragging = item;
                    setTimeout(() => item.classList.add('dragging'), 0);
                    e.dataTransfer.effectAllowed = 'move';
                });

                item.addEventListener('dragover', e => {
                    e.preventDefault();
                    const rect = item.getBoundingClientRect();
                    const mid = rect.top + rect.height / 2;
                    if (e.clientY < mid) {
                        if (dragging !== item) list.insertBefore(dragging, item);
                    } else {
                        if (dragging !== item.nextSibling) list.insertBefore(dragging, item
                        .nextSibling);
                    }
                });

                item.addEventListener('dragend', () => {
                    item.classList.remove('dragging');
                    dragging = null;

                    const order = [...list.querySelectorAll('[data-id]')].map(el => parseInt(el.dataset
                        .id));

                    const container = item.closest('.rounded-2xl');
                    const overlay = document.createElement('div');
                    overlay.className =
                        'absolute inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center rounded-2xl transition-opacity';
                    overlay.innerHTML =
                        '<div class="flex items-center gap-2 text-blue-400 font-medium"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving ordering...</div>';
                    container.appendChild(overlay);

                    fetch('{{ route('admin.videos.reorder') }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                                .content
                        },
                        body: JSON.stringify({
                            order
                        })
                    }).then(() => {
                        overlay.style.opacity = '0';
                        setTimeout(() => overlay.remove(), 300);
                    });
                });
            });
        })();
    </script>
@endsection
