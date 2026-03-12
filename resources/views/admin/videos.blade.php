@extends('layouts.admin')
@section('title', __('Video Player Library'))

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-6xl mx-auto">
        <div
            class="flex flex-col md:flex-row justify-between items-center bg-gray-100 border border-gray-400 px-4 py-3 rounded-t shadow-sm">
            <div>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <img src="{{ asset('favicon.svg') }}" class="w-5 h-5" alt="Logo">
                    {{ __('Media Library & Playlist') }}
                </h1>
            </div>
        </div>

        <div
            class="flex flex-col lg:flex-row shadow-2xl border-x border-b border-gray-400 rounded-b bg-[#F0F0F0] overflow-hidden">
            {{-- Left Column: Player & Meta --}}
            <div class="lg:w-1/2 flex flex-col border-r border-gray-400">
                {{-- VLC Player Container --}}
                <div
                    class="relative bg-black w-full aspect-video flex-grow flex items-center justify-center overflow-hidden">
                    @if ($videos->isNotEmpty())
                        <video id="preview-player" class="w-full h-full object-contain">
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
                            <span class="text-gray-400 font-bold text-xl block">{{ __('Mainan media player') }}</span>
                            <span class="text-gray-500 text-sm mt-1">{{ __('No media selected') }}</span>
                        </div>
                    @endif
                </div>

                {{-- VLC Bottom Controls Bar --}}
                <div class="bg-gray-200 px-4 py-2 flex flex-col gap-1 border-t border-gray-400 shadow-inner">
                    <div class="w-full flex items-center gap-3 py-1">
                        <span id="ctrl-current-time"
                            class="text-[10px] sm:text-xs text-gray-700 w-10 text-right cursor-default hover:text-black font-semibold">00:00</span>
                        <div id="ctrl-progress-bar"
                            class="flex-1 h-3 bg-gray-300 border border-gray-400 rounded-full group cursor-pointer relative shadow-inner flex items-center hover:bg-gray-400 transition-colors">
                            <div id="ctrl-progress-fill"
                                class="absolute left-0 top-0 bottom-0 bg-orange-400 rounded-full opacity-70" style="width: 0%">
                            </div>
                            <div id="ctrl-progress-thumb"
                                class="w-4 h-4 bg-gray-100 hover:bg-white border border-gray-500 rounded-full absolute shadow -ml-2 group-hover:scale-125 transition-transform"
                                style="left: 0%">
                            </div>
                        </div>
                        <span id="ctrl-duration"
                            class="text-[10px] sm:text-xs text-gray-700 w-10 cursor-default hover:text-black font-semibold">--:--</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <button id="ctrl-play-btn"
                                class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-black transition">
                                <svg id="ctrl-play-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </button>
                            <div class="flex gap-1 sm:gap-2">
                                <button id="ctrl-prev-btn"
                                    class="w-6 h-6 flex items-center justify-center text-gray-700 hover:text-black"><svg
                                        class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z" />
                                    </svg></button>
                                <button id="ctrl-stop-btn"
                                    class="w-6 h-6 flex items-center justify-center text-gray-700 hover:text-black"><svg
                                        class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 6h12v12H6z" />
                                    </svg></button>
                                <button id="ctrl-next-btn"
                                    class="w-6 h-6 flex items-center justify-center text-gray-700 hover:text-black"><svg
                                        class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z" />
                                    </svg></button>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button id="ctrl-speed-btn"
                                class="text-[10px] font-semibold px-2 py-0.5 bg-gray-300 rounded text-gray-700 hidden lg:block border border-gray-400 shadow-inner hover:bg-gray-400 transition-colors cursor-pointer">1.00x</button>
                            <div class="flex items-center gap-2">
                                <button id="ctrl-mute-btn" class="text-gray-700 hover:text-black"><svg class="w-4 h-4"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
                                    </svg></button>
                                <div id="ctrl-volume-bar"
                                    class="w-20 h-1.5 bg-gray-300 border border-gray-400 rounded-full relative shadow-inner flex items-center cursor-pointer">
                                    <div id="ctrl-volume-fill" class="absolute left-0 top-0 bottom-0 bg-blue-500 rounded-full opacity-60" style="width: 70%">
                                    </div>
                                    <div id="ctrl-volume-thumb"
                                        class="w-3 h-3 bg-gray-100 border border-gray-500 rounded-full absolute shadow -ml-1.5" style="left: 70%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Middle Column: Playlist --}}
            <div class="lg:w-1/4 flex flex-col bg-white border-r border-gray-400">

                {{-- Playlist Header --}}
                <div
                    class="bg-gray-100 px-4 py-2 border-b border-gray-300 flex justify-between items-center text-xs font-bold text-gray-800 uppercase shadow-sm z-10">
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-500" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M22 6H12l-2-2H2v16h20V6z" />
                        </svg> {{ __('Playlist') }}</span>
                    <span
                        class="font-normal text-[10px] text-orange-600 bg-orange-100 border border-orange-200 px-2 py-0.5 rounded">{{ $videos->count() }}
                        {{ __('items') }}</span>
                </div>

                {{-- Playlist Queue --}}
                <div class="flex-1 overflow-y-auto flex flex-col h-[400px] lg:h-auto border-b border-gray-300 bg-white"
                    id="video-list">
                    @if ($videos->isEmpty())
                        <div class="flex-1 flex flex-col gap-2 items-center justify-center text-gray-400 text-sm italic py-10"
                            id="playlist-empty-state">
                            <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                            </svg>
                            {{ __('Empty playlist') }}<br>
                            <span class="text-[10px]">{{ __('Drag from Media Library') }}</span>
                        </div>
                    @else
                        @foreach ($videos as $index => $video)
                            <div class="flex items-center border-b border-gray-200 hover:bg-blue-50 cursor-move transition-colors px-3 py-2.5 gap-2 shrink-0"
                                data-id="{{ $video->id }}" data-type="playlist">
                                <div class="flex items-center gap-2 min-w-0 flex-1 cursor-pointer"
                                    onclick="playVideo('{{ asset('storage/' . $video->path) }}', '{{ addslashes($video->title) }}', '{{ addslashes($video->filename) }}')">
                                    <span class="text-xs text-gray-500 w-5 text-right font-mono shrink-0">{{ $index + 1 }}</span>
                                    <div class="truncate">
                                        <div class="text-xs font-bold text-gray-800 truncate">{{ $video->title }}</div>
                                        <div class="text-[10px] text-gray-500 truncate">{{ $video->filename }}</div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 font-mono shrink-0">{{ $video->formatted_duration }}</span>
                                <div class="flex items-center gap-0.5 shrink-0">
                                    <flux:button
                                        onclick="playVideo('{{ asset('storage/' . $video->path) }}', '{{ addslashes($video->title) }}', '{{ addslashes($video->filename) }}')"
                                        variant="subtle" size="xs" square icon="play" aria-label="Play" />
                                    <flux:button
                                        onclick="openEditModal({{ $video->id }}, '{{ addslashes($video->title) }}')"
                                        variant="subtle" size="xs" square icon="pencil-square" aria-label="Edit" />
                                    <form action="{{ route('admin.videos.destroy', $video) }}" method="POST"
                                        onsubmit="event.preventDefault(); Swal.fire({ title: 'Remove {{ addslashes($video->title) }}?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, remove it!' }).then((result) => { if (result.isConfirmed) this.submit(); });"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="subtle" size="xs" square icon="trash" aria-label="Delete" />
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Right Column: Media Library --}}
            <div class="lg:w-1/4 flex flex-col bg-white">
                {{-- Library Header --}}
                <div
                    class="bg-gray-100 px-4 py-2 border-b border-gray-300 flex justify-between items-center text-xs font-bold text-gray-800 uppercase shadow-sm z-10">
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 text-gray-500" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                        </svg> {{ __('Library') }}</span>
                    <span
                        class="font-normal text-[10px] text-blue-600 bg-blue-100 border border-blue-200 px-2 py-0.5 rounded">{{ $allVideos->count() }}
                        {{ __('items') }}</span>
                </div>

                {{-- Library List --}}
                <div class="flex-1 overflow-y-auto h-[300px] lg:max-h-[500px] border-b border-gray-300 bg-white block"
                    id="media-list">
                    @foreach ($allVideos as $v)
                        <div class="flex items-center border-b border-gray-100 hover:bg-gray-50 cursor-grab active:cursor-grabbing transition-colors px-3 py-2 gap-2 shrink-0"
                            draggable="true" data-id="{{ $v->id }}" data-type="media">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <div class="truncate">
                                    <div class="text-xs font-bold text-gray-700 truncate">{{ $v->title }}</div>
                                    <div class="text-[10px] text-gray-400 truncate">{{ $v->filename }}</div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 font-mono shrink-0">{{ $v->formatted_duration }}</span>
                            <div class="flex items-center gap-0.5 shrink-0">
                                <flux:button
                                    onclick="openEditModal({{ $v->id }}, '{{ addslashes($v->title) }}')"
                                    variant="subtle" size="xs" square icon="pencil-square" aria-label="Edit" />
                                <form action="{{ route('admin.videos.force-destroy', $v) }}" method="POST"
                                    onsubmit="event.preventDefault(); Swal.fire({ title: '{{ __('Permanently delete') }} {{ addslashes($v->title) }}?', text: '{{ __('This will remove the video file and database record. This cannot be undone.') }}', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: '{{ __('Yes, delete permanently!') }}' }).then((result) => { if (result.isConfirmed) this.submit(); });"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="subtle" size="xs" square icon="trash" aria-label="Delete" />
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Compact Upload Form --}}
                <div class="bg-gray-50 p-4 border-t border-gray-300 shadow-inner mt-auto relative">
                    <h3 class="text-xs font-bold text-gray-800 uppercase mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4 text-orange-50" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        {{ __('Upload New') }}
                    </h3>
                    <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-3">
                        @csrf

                        @if ($errors->any())
                            <div
                                class="p-2 bg-red-100 border border-red-200 rounded text-red-700 text-[10px] font-semibold">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <input type="text" name="title" value="{{ old('title') }}"
                                placeholder="{{ __('Video Title (blank = use filename)') }}"
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
                                <span class="file-label-text">{{ __('Select .mp4 File...') }}</span>
                            </div>
                        </div>
                        @error('video')
                            <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center gap-2 mt-2 pt-1">
                            <input type="checkbox" id="rotate-portrait" name="rotate" value="1"
                                class="rounded border-gray-400 text-orange-600 focus:ring-orange-500">
                            <label for="rotate-portrait" class="text-[10px] font-bold text-gray-800">{{ __('Rotate Video 90° for Portrait TV') }}</label>
                        </div>

                        <div class="pt-1">
                            <button type="submit"
                                class="w-full py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 hover:border-gray-500 shadow text-gray-800 font-bold active:bg-gray-400 text-xs rounded uppercase tracking-wider transition-all">
                                {{ __('Upload & Library') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Video Modal --}}
    <div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-2xl border border-gray-300 w-full max-w-sm mx-4">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    {{ __('Edit Video') }}
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
            <form id="edit-modal-form" onsubmit="saveVideo(event)" class="p-4 space-y-4">
                <input type="hidden" id="edit-video-id">
                <div>
                    <label for="edit-title" class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ __('Title') }}</label>
                    <input type="text" id="edit-title" name="title" required
                        class="w-full text-xs px-3 py-2 border border-gray-400 shadow-inner rounded focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit-rotate" name="rotate" value="1"
                        class="rounded border-gray-400 text-orange-600 focus:ring-orange-500">
                    <label for="edit-rotate" class="text-xs font-bold text-gray-700">{{ __('Rotate Video 90° for Portrait TV') }}</label>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-xs font-bold uppercase bg-gray-200 hover:bg-gray-300 border border-gray-400 rounded text-gray-700 transition-colors">{{ __('Cancel') }}</button>
                    <button type="submit" id="edit-submit-btn"
                        class="px-4 py-2 text-xs font-bold uppercase bg-orange-500 hover:bg-orange-600 border border-orange-600 rounded text-white shadow transition-colors">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        [draggable=true] {
            cursor: move;
        }

        .cursor-grab {
            cursor: grab;
        }

        .cursor-grabbing {
            cursor: grabbing;
        }

        .dragging {
            opacity: 0.5;
            background: #EFF6FF !important;
        }

        .drop-target {
            background-color: #DBEAFE !important;
            border: 2px dashed #3B82F6 !important;
        }

        /* Custom VLC-ish scrollbar */
        #video-list::-webkit-scrollbar,
        #media-list::-webkit-scrollbar {
            width: 12px;
        }

        #video-list::-webkit-scrollbar-track,
        #media-list::-webkit-scrollbar-track {
            background: #F0F0F0;
            border-left: 1px solid #D1D5DB;
        }

        #video-list::-webkit-scrollbar-thumb,
        #media-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border: 2px solid #F0F0F0;
            border-radius: 6px;
        }

        #video-list::-webkit-scrollbar-thumb:hover,
        #media-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        document.querySelector('.file-input-vlc').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : "Select .mp4 File...";
            document.querySelector('.file-label-text').innerText = fileName;
        });

        // Player controls
        (function() {
            const player = document.getElementById('preview-player');
            if (!player) return;

            const playBtn = document.getElementById('ctrl-play-btn');
            const playIcon = document.getElementById('ctrl-play-icon');
            const stopBtn = document.getElementById('ctrl-stop-btn');
            const prevBtn = document.getElementById('ctrl-prev-btn');
            const nextBtn = document.getElementById('ctrl-next-btn');
            const currentTimeEl = document.getElementById('ctrl-current-time');
            const durationEl = document.getElementById('ctrl-duration');
            const progressBar = document.getElementById('ctrl-progress-bar');
            const progressFill = document.getElementById('ctrl-progress-fill');
            const progressThumb = document.getElementById('ctrl-progress-thumb');
            const speedBtn = document.getElementById('ctrl-speed-btn');
            const muteBtn = document.getElementById('ctrl-mute-btn');
            const volumeBar = document.getElementById('ctrl-volume-bar');
            const volumeFill = document.getElementById('ctrl-volume-fill');
            const volumeThumb = document.getElementById('ctrl-volume-thumb');

            const speeds = [0.5, 0.75, 1.0, 1.25, 1.5, 2.0];
            let speedIndex = 2;

            function formatTime(seconds) {
                if (isNaN(seconds) || !isFinite(seconds)) return '--:--';
                const m = Math.floor(seconds / 60);
                const s = Math.floor(seconds % 60);
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }

            function updatePlayIcon() {
                const path = player.paused
                    ? 'M8 5v14l11-7z'
                    : 'M6 19h4V5H6v14zm8-14v14h4V5h-4z';
                playIcon.innerHTML = '<path d="' + path + '" />';
            }

            function updateProgress() {
                if (!player.duration) return;
                const pct = (player.currentTime / player.duration) * 100;
                progressFill.style.width = pct + '%';
                progressThumb.style.left = pct + '%';
                currentTimeEl.textContent = formatTime(player.currentTime);
            }

            // Play / Pause
            playBtn.addEventListener('click', function() {
                if (player.paused) {
                    player.play().catch(() => {});
                } else {
                    player.pause();
                }
            });

            // Also toggle on video click
            player.addEventListener('click', function() {
                if (player.paused) {
                    player.play().catch(() => {});
                } else {
                    player.pause();
                }
            });

            player.addEventListener('play', updatePlayIcon);
            player.addEventListener('pause', updatePlayIcon);
            player.addEventListener('timeupdate', updateProgress);
            player.addEventListener('loadedmetadata', function() {
                durationEl.textContent = formatTime(player.duration);
                updateProgress();
                updatePlayIcon();
            });

            // Stop
            stopBtn.addEventListener('click', function() {
                player.pause();
                player.currentTime = 0;
            });

            // Seek via progress bar
            function seekFromEvent(e) {
                const rect = progressBar.getBoundingClientRect();
                const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                if (player.duration) {
                    player.currentTime = pct * player.duration;
                }
            }

            let seekDragging = false;
            progressBar.addEventListener('mousedown', function(e) {
                seekDragging = true;
                seekFromEvent(e);
            });
            document.addEventListener('mousemove', function(e) {
                if (seekDragging) seekFromEvent(e);
            });
            document.addEventListener('mouseup', function() {
                seekDragging = false;
            });

            // Speed
            if (speedBtn) {
                speedBtn.addEventListener('click', function() {
                    speedIndex = (speedIndex + 1) % speeds.length;
                    player.playbackRate = speeds[speedIndex];
                    speedBtn.textContent = speeds[speedIndex].toFixed(2) + 'x';
                });
            }

            // Volume
            function setVolumeFromEvent(e) {
                const rect = volumeBar.getBoundingClientRect();
                const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                player.volume = pct;
                player.muted = false;
                volumeFill.style.width = (pct * 100) + '%';
                volumeThumb.style.left = (pct * 100) + '%';
            }

            let volDragging = false;
            volumeBar.addEventListener('mousedown', function(e) {
                volDragging = true;
                setVolumeFromEvent(e);
            });
            document.addEventListener('mousemove', function(e) {
                if (volDragging) setVolumeFromEvent(e);
            });
            document.addEventListener('mouseup', function() {
                volDragging = false;
            });

            // Mute toggle
            muteBtn.addEventListener('click', function() {
                player.muted = !player.muted;
                if (player.muted) {
                    volumeFill.style.width = '0%';
                    volumeThumb.style.left = '0%';
                } else {
                    volumeFill.style.width = (player.volume * 100) + '%';
                    volumeThumb.style.left = (player.volume * 100) + '%';
                }
            });

            // Set initial volume
            player.volume = 0.7;

            // Prev / Next playlist navigation
            function getPlaylistItems() {
                return [...document.querySelectorAll('#video-list [data-id][data-type=playlist]')];
            }

            function getCurrentIndex() {
                const items = getPlaylistItems();
                const currentSrc = document.getElementById('preview-source').src;
                return items.findIndex(item => {
                    const onclick = item.querySelector('.cursor-pointer')?.getAttribute('onclick') || '';
                    return currentSrc.includes(onclick.match(/'([^']+)'/)?.[1] || '___no_match___');
                });
            }

            function playItemAtIndex(idx) {
                const items = getPlaylistItems();
                if (idx < 0 || idx >= items.length) return;
                const clickArea = items[idx].querySelector('.cursor-pointer');
                if (clickArea) clickArea.click();
            }

            prevBtn.addEventListener('click', function() {
                const idx = getCurrentIndex();
                if (idx > 0) {
                    playItemAtIndex(idx - 1);
                } else if (player.currentTime > 3) {
                    player.currentTime = 0;
                }
            });

            nextBtn.addEventListener('click', function() {
                const idx = getCurrentIndex();
                playItemAtIndex(idx + 1);
            });

            // Expose playVideo globally
            window.playVideo = function(url, title, filename) {
                const source = document.getElementById('preview-source');
                source.src = url;
                player.load();
                player.play().catch(() => {});
                const osd = document.getElementById('osd-title');
                if (osd) osd.innerText = title;
            };
        })();

        function openEditModal(videoId, currentTitle) {
            document.getElementById('edit-video-id').value = videoId;
            document.getElementById('edit-title').value = currentTitle;
            document.getElementById('edit-rotate').checked = false;
            const modal = document.getElementById('edit-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('edit-title').focus();
        }

        function closeEditModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function saveVideo(e) {
            e.preventDefault();
            const videoId = document.getElementById('edit-video-id').value;
            const title = document.getElementById('edit-title').value.trim();
            if (!title) return;

            const rotate = document.getElementById('edit-rotate').checked;
            const submitBtn = document.getElementById('edit-submit-btn');
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;

            fetch(`/admin/videos/${videoId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ title, rotate })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                submitBtn.textContent = 'Save';
                submitBtn.disabled = false;
            });
        }

        // Close modal on backdrop click
        document.getElementById('edit-modal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeEditModal();
        });

        // Drag and drop logic
        (function() {
            const playlist = document.getElementById('video-list');
            const mediaList = document.getElementById('media-list');
            if (!playlist || !mediaList) return;

            let dragging = null;

            // Setup Media Library items
            mediaList.querySelectorAll('[data-id]').forEach(item => {
                item.addEventListener('dragstart', (e) => {
                    dragging = item;
                    e.dataTransfer.setData('text/plain', item.dataset.id);
                    e.dataTransfer.setData('source', 'media');
                    setTimeout(() => item.classList.add('dragging'), 0);
                });

                item.addEventListener('dragend', () => {
                    item.classList.remove('dragging');
                    dragging = null;
                    playlist.classList.remove('drop-target');
                });
            });

            // Setup Playlist items (for reordering)
            function setupPlaylistItem(item) {
                item.setAttribute('draggable', true);

                item.addEventListener('dragstart', (e) => {
                    if (e.target.tagName.toLowerCase() === 'button' || e.target.closest('button')) {
                        e.preventDefault();
                        return;
                    }
                    dragging = item;
                    e.dataTransfer.setData('source', 'playlist');
                    setTimeout(() => item.classList.add('dragging'), 0);
                    e.dataTransfer.effectAllowed = 'move';
                });

                item.addEventListener('dragover', e => {
                    e.preventDefault();
                    if (!dragging) return;

                    const rect = item.getBoundingClientRect();
                    const mid = rect.top + rect.height / 2;

                    if (dragging.dataset.type === 'playlist') {
                        if (e.clientY < mid) {
                            if (dragging !== item) playlist.insertBefore(dragging, item);
                        } else {
                            if (dragging !== item.nextSibling) playlist.insertBefore(dragging, item
                            .nextSibling);
                        }
                    }
                });

                item.addEventListener('dragend', () => {
                    if (!dragging) return;
                    item.classList.remove('dragging');

                    if (dragging.dataset.type === 'playlist') {
                        saveOrder();
                    }
                    dragging = null;
                });
            }

            playlist.querySelectorAll('[data-id]').forEach(setupPlaylistItem);

            // Playlist drop zone handler for media items
            playlist.addEventListener('dragover', e => {
                e.preventDefault();
                if (dragging && dragging.dataset.type === 'media') {
                    playlist.classList.add('drop-target');
                }
            });

            playlist.addEventListener('dragleave', () => {
                playlist.classList.remove('drop-target');
            });

            playlist.addEventListener('drop', e => {
                e.preventDefault();
                playlist.classList.remove('drop-target');

                const source = e.dataTransfer.getData('source');
                const id = e.dataTransfer.getData('text/plain');

                if (source === 'media') {
                    addToPlaylist(id);
                }
            });

            function addToPlaylist(videoId) {
                const overlay = createOverlay('Adding...');
                playlist.appendChild(overlay);

                const formData = new FormData();
                formData.append('existing_video_id', videoId);
                formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

                fetch('{{ route('admin.videos.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        window.location.reload(); // Simple reload to refresh the playlist
                    })
                    .catch(err => {
                        console.error(err);
                        overlay.remove();
                    });
            }

            function saveOrder() {
                const order = [...playlist.querySelectorAll('[data-id][data-type=playlist]')].map(el => parseInt(el
                    .dataset.id));
                const overlay = createOverlay('Saving Order...');
                playlist.appendChild(overlay);

                fetch('{{ route('admin.videos.reorder') }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        order
                    })
                }).then(() => {
                    // refresh index numbers
                    playlist.querySelectorAll('[data-id][data-type=playlist]').forEach((el, idx) => {
                        const numSpan = el.querySelector('.font-mono');
                        if (numSpan) numSpan.innerText = idx + 1;
                    });
                    overlay.remove();
                });
            }

            function createOverlay(text) {
                const overlay = document.createElement('div');
                overlay.className =
                    'absolute inset-0 bg-white/50 backdrop-blur-sm z-50 flex items-center justify-center transition-opacity';
                overlay.innerHTML =
                    `<span class="text-orange-600 font-bold bg-white px-3 py-1 border border-orange-200 rounded shadow">${text}</span>`;
                playlist.parentElement.style.position = 'relative';
                return overlay;
            }
        })();
    </script>
@endsection
