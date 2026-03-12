@extends('layouts.admin')
@section('title', __('Image Slides'))

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-6xl mx-auto">
        <div
            class="flex flex-col md:flex-row justify-between items-center bg-gray-100 border border-gray-400 px-4 py-3 rounded-t shadow-sm">
            <div>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <img src="{{ asset('favicon.svg') }}" class="w-5 h-5" alt="Logo">
                    {{ __('Image Slides') }}
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Fullscreen image slideshow shown behind the schedule overlay on the player.') }}</p>
            </div>
        </div>

        <div
            class="flex flex-col lg:flex-row shadow-2xl border-x border-b border-gray-400 rounded-b bg-[#F0F0F0] overflow-hidden">

            {{-- Left Column: Image Preview --}}
            <div class="lg:w-1/2 flex flex-col border-r border-gray-400">
                <div
                    class="relative bg-black w-full aspect-video flex-grow flex items-center justify-center overflow-hidden">
                    @if ($slides->isNotEmpty())
                        <img id="preview-image"
                            src="{{ asset('storage/' . $slides->first()->path) }}"
                            alt="{{ $slides->first()->title }}"
                            class="w-full h-full object-contain">
                        <div class="absolute top-4 left-4 pointer-events-none">
                            <span id="osd-title"
                                class="text-white text-2xl font-bold bg-black/50 px-2 py-1 rounded opacity-70">
                                {{ $slides->first()->title }}
                            </span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-center p-6">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                class="w-24 h-24 text-orange-500/30 mb-2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-400 font-bold text-xl block">{{ __('No images yet') }}</span>
                            <span class="text-gray-500 text-sm mt-1">{{ __('Upload images to build the slideshow') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Preview info bar --}}
                <div class="bg-gray-200 px-4 py-2 flex items-center justify-between border-t border-gray-400 shadow-inner">
                    <span id="preview-duration-label" class="text-xs text-gray-600 font-semibold">
                        @if ($slides->isNotEmpty())
                            {{ __('Duration') }}: {{ $slides->first()->duration }}s
                        @else
                            {{ __('No image selected') }}
                        @endif
                    </span>
                    <span class="text-[10px] text-gray-400">{{ __('Click a slide to preview') }}</span>
                </div>
            </div>

            {{-- Right Column: Slides List + Upload --}}
            <div class="lg:w-1/2 flex flex-col">

                {{-- Slides List Header --}}
                <div
                    class="bg-gray-100 px-4 py-2 border-b border-gray-300 flex justify-between items-center text-xs font-bold text-gray-800 uppercase shadow-sm z-10">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        {{ __('Slide Order') }}
                    </span>
                    <span
                        class="font-normal text-[10px] text-orange-600 bg-orange-100 border border-orange-200 px-2 py-0.5 rounded">{{ $slides->count() }}
                        {{ __('slides') }}</span>
                </div>

                {{-- Slides List --}}
                <div class="flex-1 overflow-y-auto flex flex-col h-[300px] lg:h-auto border-b border-gray-300 bg-white"
                    id="slide-list">
                    @if ($slides->isEmpty())
                        <div class="flex-1 flex flex-col gap-2 items-center justify-center text-gray-400 text-sm italic py-10">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('No slides yet') }}
                        </div>
                    @else
                        @foreach ($slides as $index => $slide)
                            <div class="flex items-center border-b border-gray-200 hover:bg-blue-50 cursor-move transition-colors px-3 py-2.5 gap-2 shrink-0"
                                draggable="true"
                                data-id="{{ $slide->id }}">
                                {{-- Thumbnail --}}
                                <div class="shrink-0 w-12 h-9 bg-gray-200 rounded overflow-hidden border border-gray-300 cursor-pointer"
                                    onclick="previewSlide('{{ asset('storage/' . $slide->path) }}', '{{ addslashes($slide->title ?? $slide->filename) }}', {{ $slide->duration }})">
                                    <img src="{{ asset('storage/' . $slide->path) }}"
                                        alt="{{ $slide->title ?? $slide->filename }}"
                                        class="w-full h-full object-cover">
                                </div>
                                {{-- Info --}}
                                <div class="flex items-center gap-2 min-w-0 flex-1 cursor-pointer"
                                    onclick="previewSlide('{{ asset('storage/' . $slide->path) }}', '{{ addslashes($slide->title ?? $slide->filename) }}', {{ $slide->duration }})">
                                    <span class="text-xs text-gray-500 w-5 text-right font-mono shrink-0">{{ $index + 1 }}</span>
                                    <div class="truncate">
                                        <div class="text-xs font-bold text-gray-800 truncate">{{ $slide->title ?? $slide->filename }}</div>
                                        <div class="text-[10px] text-gray-500 truncate">{{ $slide->filename }}</div>
                                    </div>
                                </div>
                                {{-- Duration badge --}}
                                <span class="text-xs text-blue-600 font-mono shrink-0 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">{{ $slide->duration }}s</span>
                                {{-- Actions --}}
                                <div class="flex items-center gap-0.5 shrink-0">
                                    <flux:button
                                        onclick="previewSlide('{{ asset('storage/' . $slide->path) }}', '{{ addslashes($slide->title ?? $slide->filename) }}', {{ $slide->duration }})"
                                        variant="subtle" size="xs" square icon="eye" aria-label="Preview" />
                                    <flux:button
                                        onclick="openEditModal({{ $slide->id }}, '{{ addslashes($slide->title ?? '') }}', {{ $slide->duration }})"
                                        variant="subtle" size="xs" square icon="pencil-square" aria-label="Edit" />
                                    <form action="{{ route('admin.image-slides.destroy', $slide) }}" method="POST"
                                        onsubmit="event.preventDefault(); Swal.fire({ title: '{{ __('Delete this slide?') }}', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: '{{ __('Yes, delete it!') }}', cancelButtonText: '{{ __('Cancel') }}' }).then((result) => { if (result.isConfirmed) this.submit(); });"
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

                {{-- Upload Form --}}
                <div class="bg-gray-50 p-4 border-t border-gray-300 shadow-inner">
                    <h3 class="text-xs font-bold text-gray-800 uppercase mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        {{ __('Upload New Slide') }}
                    </h3>

                    <form action="{{ route('admin.image-slides.store') }}" method="POST" enctype="multipart/form-data"
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
                                placeholder="{{ __('Slide Title (blank = use filename)') }}"
                                class="w-full text-xs px-3 py-2 border border-gray-400 shadow-inner rounded focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white">
                        </div>

                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input-slide">
                                <div
                                    class="w-full text-xs px-3 py-2 border border-dashed border-gray-400 shadow-sm rounded bg-white text-gray-600 text-center flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z" />
                                    </svg>
                                    <span class="file-label-text">{{ __('Select Image...') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <label class="text-xs font-bold text-gray-700 whitespace-nowrap">{{ __('Duration') }} (s):</label>
                                <input type="number" name="duration" value="{{ old('duration', 5) }}" min="1" max="300" required
                                    class="w-16 text-xs px-2 py-2 border border-gray-400 shadow-inner rounded focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white text-center">
                            </div>
                        </div>

                        @error('image')
                            <p class="text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                        @error('duration')
                            <p class="text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="rotate-upload" name="rotate" value="1"
                                class="rounded border-gray-400 text-orange-600 focus:ring-orange-500">
                            <label for="rotate-upload" class="text-[10px] font-bold text-gray-800">{{ __('Rotate Image 90° clockwise') }}</label>
                        </div>

                        <div class="pt-1">
                            <button type="submit"
                                class="w-full py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 hover:border-gray-500 shadow text-gray-800 font-bold active:bg-gray-400 text-xs rounded uppercase tracking-wider transition-all">
                                {{ __('Upload Slide') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-2xl border border-gray-300 w-full max-w-sm mx-4">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    {{ __('Edit Slide') }}
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
            <form id="edit-form" onsubmit="saveSlide(event)" class="p-4 space-y-4">
                <input type="hidden" id="edit-slide-id">
                <div>
                    <label for="edit-title" class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ __('Title') }}</label>
                    <input type="text" id="edit-title" name="title"
                        class="w-full text-xs px-3 py-2 border border-gray-400 shadow-inner rounded focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white">
                </div>
                <div>
                    <label for="edit-duration" class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ __('Duration') }} ({{ __('seconds') }})</label>
                    <input type="number" id="edit-duration" name="duration" min="1" max="300" required
                        class="w-full text-xs px-3 py-2 border border-gray-400 shadow-inner rounded focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit-rotate" name="rotate" value="1"
                        class="rounded border-gray-400 text-orange-600 focus:ring-orange-500">
                    <label for="edit-rotate" class="text-xs font-bold text-gray-700">{{ __('Rotate Image 90° clockwise') }}</label>
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
        [draggable=true] { cursor: move; }
        .dragging { opacity: 0.5; background: #EFF6FF !important; }
        .drop-target { background-color: #DBEAFE !important; border: 2px dashed #3B82F6 !important; }

        #slide-list::-webkit-scrollbar { width: 12px; }
        #slide-list::-webkit-scrollbar-track { background: #F0F0F0; border-left: 1px solid #D1D5DB; }
        #slide-list::-webkit-scrollbar-thumb { background: #cbd5e1; border: 2px solid #F0F0F0; border-radius: 6px; }
        #slide-list::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <script>
        // File input label
        document.querySelector('.file-input-slide').addEventListener('change', function(e) {
            const name = e.target.files[0] ? e.target.files[0].name : '{{ __('Select Image...') }}';
            document.querySelector('.file-label-text').innerText = name;
        });

        function previewSlide(url, title, duration) {
            const img = document.getElementById('preview-image');
            const osd = document.getElementById('osd-title');
            const dur = document.getElementById('preview-duration-label');
            if (img) { img.src = url; img.alt = title; }
            if (osd) osd.innerText = title;
            if (dur) dur.innerText = '{{ __('Duration') }}: ' + duration + 's';
        }

        function openEditModal(slideId, currentTitle, currentDuration) {
            document.getElementById('edit-slide-id').value = slideId;
            document.getElementById('edit-title').value = currentTitle;
            document.getElementById('edit-duration').value = currentDuration;
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

        function saveSlide(e) {
            e.preventDefault();
            const slideId = document.getElementById('edit-slide-id').value;
            const title = document.getElementById('edit-title').value.trim();
            const duration = parseInt(document.getElementById('edit-duration').value, 10);
            const rotate = document.getElementById('edit-rotate').checked;
            const submitBtn = document.getElementById('edit-submit-btn');

            submitBtn.textContent = '{{ __('Saving...') }}';
            submitBtn.disabled = true;

            fetch(`/admin/image-slides/${slideId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ title, duration, rotate })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) { window.location.reload(); }
            })
            .catch(() => {
                submitBtn.textContent = '{{ __('Save') }}';
                submitBtn.disabled = false;
            });
        }

        document.getElementById('edit-modal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeEditModal();
        });

        // Drag-and-drop reorder
        (function() {
            const list = document.getElementById('slide-list');
            if (!list) return;

            let dragging = null;

            list.querySelectorAll('[data-id]').forEach(function(item) {
                item.addEventListener('dragstart', function(e) {
                    dragging = item;
                    e.dataTransfer.effectAllowed = 'move';
                    setTimeout(function() { item.classList.add('dragging'); }, 0);
                });
                item.addEventListener('dragend', function() {
                    item.classList.remove('dragging');
                    dragging = null;
                    list.querySelectorAll('[data-id]').forEach(function(i) {
                        i.classList.remove('drop-target');
                    });
                });
                item.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    if (dragging && item !== dragging) {
                        item.classList.add('drop-target');
                    }
                });
                item.addEventListener('dragleave', function() {
                    item.classList.remove('drop-target');
                });
                item.addEventListener('drop', function(e) {
                    e.preventDefault();
                    item.classList.remove('drop-target');
                    if (dragging && item !== dragging) {
                        const allItems = [...list.querySelectorAll('[data-id]')];
                        const fromIdx = allItems.indexOf(dragging);
                        const toIdx = allItems.indexOf(item);
                        if (fromIdx < toIdx) {
                            list.insertBefore(dragging, item.nextSibling);
                        } else {
                            list.insertBefore(dragging, item);
                        }
                        saveOrder();
                    }
                });
            });

            function saveOrder() {
                const order = [...list.querySelectorAll('[data-id]')].map(function(i) {
                    return parseInt(i.dataset.id, 10);
                });
                fetch('/admin/image-slides/reorder', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ order })
                }).catch(function(err) { console.error('Reorder failed', err); });
            }
        })();
    </script>
@endsection
