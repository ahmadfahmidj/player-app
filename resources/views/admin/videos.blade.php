@extends('layouts.admin')
@section('title', 'Video Player Library')

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-6xl mx-auto">
        <div
            class="flex flex-col md:flex-row justify-between items-center bg-gray-100 border border-gray-400 px-4 py-3 rounded-t shadow-sm">
            <div>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L2 22H22L12 2Z" />
                    </svg>
                    Media Library & Playlist
                </h1>
            </div>
        </div>

        <div
            class="flex flex-col lg:flex-row shadow-2xl border-x border-b border-gray-400 rounded-b bg-[#F0F0F0] overflow-hidden">
            {{-- Left Column: Player & Meta --}}
            <div class="lg:w-2/3 flex flex-col border-r border-gray-400">
                {{-- VLC Player Container --}}
                <div
                    class="relative bg-black w-full aspect-video flex-grow flex items-center justify-center overflow-hidden">
                    @if ($videos->isNotEmpty())
                        <video id="preview-player" class="w-full h-full object-contain" controls>
                            <source id="preview-source" src="{{ asset('storage/' . $videos->first()->path) }}"
                                type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="absolute top-4 left-4 pointer-events-none">
                            <span id="osd-title"
                                class="text-white text-3xl font-bold bg-black/50 px-2 py-1 rounded opacity-70">
                                {{ $videos->first()->title }}
                            </span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-center p-6">
                            <svg viewBox="0 0 24 24" fill="currentColor"
                                class="w-24 h-24 text-orange-500/50 mb-2 drop-shadow-lg">
                                <path d="M12 2L2 22H22L12 2Z" />
                            </svg>
                            <span class="text-gray-400 font-bold text-xl block">VLC media player</span>
                            <span class="text-gray-500 text-sm mt-1">No media selected</span>
                        </div>
                    @endif
                </div>

                {{-- VLC Bottom Controls Bar (Mock) --}}
                <div class="bg-gray-200 px-4 py-2 flex flex-col gap-1 border-t border-gray-400 shadow-inner">
                    <div class="w-full flex items-center gap-3 py-1">
                        <span
                            class="text-[10px] sm:text-xs text-gray-700 w-10 text-right cursor-default hover:text-black font-semibold">00:00</span>
                        <div
                            class="flex-1 h-3 bg-gray-300 border border-gray-400 rounded-full group cursor-pointer relative shadow-inner flex items-center hover:bg-gray-400 transition-colors">
                            <div
                                class="w-4 h-4 bg-gray-100 hover:bg-white border border-gray-500 rounded-full absolute left-0 shadow -ml-2 group-hover:scale-125 transition-transform">
                            </div>
                        </div>
                        <span
                            class="text-[10px] sm:text-xs text-gray-700 w-10 cursor-default hover:text-black font-semibold">--:--</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <button
                                class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-black transition cursor-not-allowed">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </button>
                            <div class="flex gap-1 sm:gap-2">
                                <button
                                    class="w-6 h-6 flex items-center justify-center text-gray-700 hover:text-black cursor-not-allowed"><svg
                                        class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z" />
                                    </svg></button>
                                <button
                                    class="w-6 h-6 flex items-center justify-center text-gray-700 hover:text-black cursor-not-allowed"><svg
                                        class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 6h12v12H6z" />
                                    </svg></button>
                                <button
                                    class="w-6 h-6 flex items-center justify-center text-gray-700 hover:text-black cursor-not-allowed"><svg
                                        class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z" />
                                    </svg></button>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="text-[10px] font-semibold px-2 py-0.5 bg-gray-300 rounded text-gray-700 hidden lg:block border border-gray-400 shadow-inner">1.00x</span>
                            <div class="flex items-center gap-2">
                                <button class="text-gray-700 hover:text-black cursor-not-allowed"><svg class="w-4 h-4"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
                                    </svg></button>
                                <div
                                    class="w-20 h-1.5 bg-gray-300 border border-gray-400 rounded-full relative shadow-inner flex items-center cursor-not-allowed">
                                    <div class="absolute left-0 top-0 bottom-0 bg-blue-500 w-[70%] rounded-full opacity-60">
                                    </div>
                                    <div
                                        class="w-3 h-3 bg-gray-100 border border-gray-500 rounded-full absolute left-[70%] shadow -ml-1.5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Upload & Playlist --}}
            <div class="lg:w-1/3 flex flex-col bg-white">

                {{-- Playlist Header --}}
                <div
                    class="bg-gray-100 px-4 py-2 border-b border-gray-300 flex justify-between items-center text-xs font-bold text-gray-800 uppercase shadow-sm z-10">
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-500" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M22 6H12l-2-2H2v16h20V6z" />
                        </svg> Playlist</span>
                    <span
                        class="font-normal text-[10px] text-orange-600 bg-orange-100 border border-orange-200 px-2 py-0.5 rounded">{{ $videos->count() }}
                        items</span>
                </div>

                {{-- Playlist Queue --}}
                <div class="flex-1 overflow-y-auto flex flex-col h-[400px] lg:h-auto border-b border-gray-300 bg-white"
                    id="video-list">
                    @if ($videos->isEmpty())
                        <div
                            class="flex-1 flex flex-col gap-2 items-center justify-center text-gray-400 text-sm italic py-10">
                            <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                            </svg>
                            Empty playlist
                        </div>
                    @else
                        @foreach ($videos as $index => $video)
                            <div class="flex flex-col group border-b border-gray-200 hover:bg-blue-50 cursor-move transition-colors"
                                data-id="{{ $video->id }}">
                                <div class="flex items-start justify-between px-3 py-2.5 w-full text-left focus:outline-none"
                                    tabindex="0"
                                    onclick="playVideo('{{ asset('storage/' . $video->path) }}', '{{ addslashes($video->title) }}', '{{ addslashes($video->filename) }}')">
                                    <div class="flex items-center gap-2 min-w-0 pointer-events-none">
                                        <span
                                            class="text-xs text-gray-500 w-5 text-right font-mono">{{ $index + 1 }}</span>
                                        <div class="truncate">
                                            <div class="text-xs font-bold text-gray-800 group-hover:text-black truncate">
                                                {{ $video->title }}</div>
                                            <div class="text-[10px] text-gray-500 truncate">{{ $video->filename }}</div>
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs text-gray-500 font-mono pointer-events-none">{{ gmdate('i:s', $video->duration) }}</span>
                                </div>
                                <div class="px-3 pb-2 hidden group-hover:flex justify-end gap-2 items-center bg-blue-50/50">
                                    <button
                                        onclick="playVideo('{{ asset('storage/' . $video->path) }}', '{{ addslashes($video->title) }}', '{{ addslashes($video->filename) }}')"
                                        class="text-[10px] font-bold uppercase px-2 py-0.5 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded border border-gray-300">Play</button>
                                    <form action="{{ route('admin.videos.destroy', $video) }}" method="POST"
                                        onsubmit="return confirm('Remove {{ addslashes($video->title) }}?')"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-[10px] font-bold uppercase px-2 py-0.5 bg-red-100 text-red-700 hover:bg-red-200 rounded border border-red-200 hover:border-red-300">Del</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Compact Upload Form --}}
                <div class="bg-gray-100 p-4 border-t border-gray-300 shadow-inner mt-auto relative">
                    <h3 class="text-xs font-bold text-gray-800 uppercase mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        Add Media
                    </h3>
                    <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-3">
                        @csrf

                        @if ($errors->any())
                            <div class="p-2 bg-red-100 border border-red-200 rounded text-red-700 text-[10px] font-semibold">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($allVideos->isNotEmpty())
                            <div class="mb-3 space-y-2 border-b border-gray-300 pb-3">
                                <select name="existing_video_id" onchange="if(this.value) { this.form.removeAttribute('enctype'); this.form.querySelector('input[name=title]').removeAttribute('required'); this.form.querySelector('input[name=video]').removeAttribute('required'); } else { this.form.setAttribute('enctype', 'multipart/form-data'); this.form.querySelector('input[name=title]').setAttribute('required', 'required'); this.form.querySelector('input[name=video]').setAttribute('required', 'required'); }" class="w-full text-xs px-3 py-2 border border-gray-400 shadow-inner rounded focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white">
                                    <option value="">-- Or Choose Existing Media --</option>
                                    @foreach($allVideos as $v)
                                        <option value="{{ $v->id }}" {{ old('existing_video_id') == $v->id ? 'selected' : '' }}>{{ $v->title }} ({{ gmdate('i:s', $v->duration) }})</option>
                                    @endforeach
                                </select>
                                @error('existing_video_id')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        <div>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                placeholder="Video Title"
                                class="w-full text-xs px-3 py-2 border border-gray-400 shadow-inner rounded focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white">
                            @error('title')
                                <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="relative w-full">
                            <input type="file" name="video" accept="video/mp4" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input-vlc">
                            <div
                                class="w-full text-xs px-3 py-2 border border-dashed border-gray-400 shadow-sm rounded bg-white text-gray-600 text-center flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors tooltip-target">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z" />
                                </svg>
                                <span class="file-label-text">Select .mp4 File...</span>
                            </div>
                        </div>
                        @error('video')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                        <div class="pt-1">
                            <button type="submit"
                                class="w-full py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 hover:border-gray-500 shadow text-gray-800 font-bold active:bg-gray-400 text-xs rounded uppercase tracking-wider transition-all">
                                Enqueue in Playlist
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        [draggable=true] {
            cursor: move;
        }

        .dragging {
            opacity: 0.5;
            background: #EFF6FF !important;
        }

        /* Custom VLC-ish scrollbar */
        #video-list::-webkit-scrollbar {
            width: 12px;
        }

        #video-list::-webkit-scrollbar-track {
            background: #F0F0F0;
            border-left: 1px solid #D1D5DB;
        }

        #video-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border: 2px solid #F0F0F0;
            border-radius: 6px;
        }

        #video-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        document.querySelector('.file-input-vlc').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : "Select .mp4 File...";
            document.querySelector('.file-label-text').innerText = fileName;
        });

        function playVideo(url, title, filename) {
            const player = document.getElementById('preview-player');
            if (!player) return;
            const source = document.getElementById('preview-source');

            source.src = url;
            player.load();
            player.play().catch(e => console.log('Autoplay prevented by browser'));

            const osd = document.getElementById('osd-title');
            if (osd) osd.innerText = title;
        }

        // Drag to reorder
        (function() {
            const list = document.getElementById('video-list');
            if (!list) return;
            let dragging = null;

            list.querySelectorAll('[data-id]').forEach(item => {
                item.setAttribute('draggable', true);

                item.addEventListener('dragstart', (e) => {
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

                    // visual cue
                    const overlay = document.createElement('div');
                    overlay.className =
                        'absolute inset-0 bg-white/50 backdrop-blur-sm z-50 flex items-center justify-center transition-opacity';
                    overlay.innerHTML =
                        '<span class="text-orange-600 font-bold bg-white px-3 py-1 border border-orange-200 rounded shadow">Saving...</span>';
                    list.parentElement.style.position = 'relative';
                    list.appendChild(overlay);

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
                        // refresh index numbers
                        list.querySelectorAll('[data-id]').forEach((el, idx) => {
                            const numSpan = el.querySelector('.font-mono');
                            if (numSpan) numSpan.innerText = idx + 1;
                        });
                        overlay.remove();
                    });
                });
            });
        })();
    </script>
@endsection
