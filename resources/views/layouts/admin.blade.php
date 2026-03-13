<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Admin')) — {{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="channel-slug" content="{{ $activeChannel->slug ?? 'default' }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<body class="bg-gray-200 min-h-screen text-gray-800 font-sans antialiased relative selection:bg-orange-500/30">
    <!-- No background orbs for VLC theme -->

    <!-- VLC-style Title & Menu Bar -->
    <nav class="relative z-20 sticky top-0 w-full bg-[#f0f0f0] border-b border-gray-300 shadow-sm">
        <!-- Title Bar -->
        {{-- <x-instruckt-toolbar /> --}}
        <div class="bg-gray-100 flex items-center justify-between px-4 py-2 border-b border-gray-300">
            <div class="flex items-center gap-4">
                <div class="flex gap-2">
                    {{-- <div class="w-3 h-3 rounded-full bg-red-500 border border-red-600"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-400 border border-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500 border border-green-600"></div> --}}
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-800">
                    <img src="{{ asset('favicon.svg') }}" class="w-4 h-4 hidden md:block" alt="Logo">
                    {{ config('app.name', 'Laravel') }} {{ __('Admin') }}
                </div>
                <div class="flex items-center gap-4">
                    <form action="{{ route('admin.channels.switch') }}" method="POST" class="flex items-center">
                        @csrf
                        <select name="channel_id" onchange="this.form.submit()"
                            class="text-xs bg-white border-2 border-orange-400 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-[0_2px_4px_rgba(255,136,0,0.1)] font-bold text-gray-900 cursor-pointer hover:border-orange-500 transition-all">
                            @foreach ($allChannels as $c)
                                <option value="{{ $c->id }}"
                                    {{ $c->id == ($activeChannel->id ?? 0) ? 'selected' : '' }}>
                                    {{ $c->name }} {{ $c->is_main ? '(' . __('Main') . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <div class="flex gap-2">
                        <button type="button"
                            onclick="document.getElementById('createChannelModal').classList.remove('hidden')"
                            class="text-xs px-2 py-1 bg-gray-200 border border-gray-400 hover:bg-gray-300 text-green-600 font-bold rounded shadow-sm transition-all flex items-center gap-1 leading-none"><svg
                                class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg> {{ __('New') }}</button>
                        @if (isset($activeChannel))
                            <button type="button" onclick="forceRefreshPlayer()"
                                class="text-xs px-2 py-1 bg-gray-200 border border-gray-400 hover:bg-gray-300 text-orange-600 font-bold rounded shadow-sm transition-all flex items-center gap-1 leading-none"><svg
                                    class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg> {{ __('Refresh TV') }}</button>
                        @endif
                        @if (isset($activeChannel))
                            <button type="button"
                                onclick="document.getElementById('editChannelSlugModal').classList.remove('hidden')"
                                class="text-xs px-2 py-1 bg-gray-200 border border-gray-400 hover:bg-gray-300 text-blue-600 font-bold rounded shadow-sm transition-all flex items-center gap-1 leading-none"><svg
                                    class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg> {{ __('Slug') }}</button>
                        @endif
                        @if (isset($activeChannel) && !$activeChannel->is_main)
                            <form action="{{ route('admin.channels.destroy', $activeChannel) }}" method="POST"
                                onsubmit="event.preventDefault(); Swal.fire({ title: '{{ __('Delete this channel?') }}', text: '{{ __('This cannot be undone.') }}', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: '{{ __('Yes, delete it!') }}', cancelButtonText: '{{ __('Cancel') }}' }).then((result) => { if (result.isConfirmed) this.submit(); });">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xs px-2 py-1 bg-gray-200 border border-gray-400 hover:bg-gray-300 text-red-600 font-bold rounded shadow-sm transition-all flex items-center gap-1 leading-none"><svg
                                        class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg> {{ __('Del') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-row items-center gap-4">
                @if (isset($storageInfo))
                    @php
                        $pct = $storageInfo['percent'];
                        $colorClass =
                            $pct >= 90
                                ? 'text-red-600 bg-red-50 border-red-200'
                                : ($pct >= 75
                                    ? 'text-orange-600 bg-orange-50 border-orange-200'
                                    : 'text-green-700 bg-green-50 border-green-200');
                        $barColor = $pct >= 90 ? 'bg-red-500' : ($pct >= 75 ? 'bg-orange-400' : 'bg-green-500');
                    @endphp
                    <div class="hidden md:flex items-center gap-2 text-[10px] font-semibold px-2.5 py-1 border rounded {{ $colorClass }}"
                        title="{{ __('Storage') }}: {{ Number::fileSize($storageInfo['used'], 2) }} / {{ Number::fileSize($storageInfo['total'], 2) }} ({{ $pct }}% {{ __('used') }})">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        <div class="flex items-center gap-1.5">
                            <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $barColor }}"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                            <span>{{ Number::fileSize($storageInfo['free'], 2) }} {{ __('free') }}</span>
                        </div>
                    </div>
                @endif
                <a href="{{ isset($activeChannel) && !$activeChannel->is_main ? route('player.channel', $activeChannel->slug) : route('player') }}"
                    target="_blank"
                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1 bg-gray-200 text-gray-700 rounded border border-gray-400 hover:bg-gray-300 transition-all shadow-sm">
                    {{ __('Player Screen') }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button type="submit"
                        class="text-xs font-medium text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 bg-gray-200 border border-gray-400 px-2 py-1 rounded shadow-sm hover:bg-white">
                        {{ __('Quit') }}
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="flex min-h-[calc(100vh-theme(spacing.16))]">
        <!-- Sidebar Navigation -->
        <aside class="w-40 shrink-0 bg-[#f0f0f0] border-r border-gray-300 sticky top-[var(--nav-height)] self-start"
            style="top: 56px; height: calc(100vh - 56px);">
            <nav class="flex flex-col gap-0.5 p-2 pt-3">
                <a href="{{ route('admin.dashboard') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('Dashboard') }}</a>
                <a href="{{ route('admin.videos') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.videos') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                    {{ __('Media Library') }}</a>
                <a href="{{ route('admin.image-slides') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.image-slides') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Image Slides') }}</a>
                <a href="{{ route('admin.event-schedules.index') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.event-schedules.*') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Event Schedules') }}</a>
                <a href="{{ route('admin.settings') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.settings') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('Tools/Prefs') }}</a>
                <span class="text-xs text-gray-400 cursor-not-allowed px-2 py-1.5 rounded flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ __('View') }}</span>
                <button type="button" onclick="document.getElementById('helpModal').classList.remove('hidden')"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 w-full text-left">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Help') }}</button>
            </nav>
        </aside>

        <main class="relative z-10 flex-1 p-6 md:p-8 space-y-6">
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: @json(session('success')),
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: @json(session('error')),
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                        });
                    });
                </script>
            @endif

            @yield('content')

        </main>
    </div>

    <!-- Edit Channel Slug Modal -->
    @if (isset($activeChannel))
        <div id="editChannelSlugModal"
            class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('Edit Channel Slug') }}</h3>
                <p class="text-sm text-gray-600 mb-3">{{ __('Channel:') }}
                    <strong>{{ $activeChannel->name }}</strong>
                </p>
                <form action="{{ route('admin.channels.update', $activeChannel) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Slug') }}</label>
                        <input type="text" name="slug" required value="{{ $activeChannel->slug }}"
                            pattern="[a-z0-9]+(-[a-z0-9]+)*"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500 mb-1">
                        <p class="text-xs text-gray-500">{{ __('Only lowercase letters, numbers, and hyphens.') }}</p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                            onclick="document.getElementById('editChannelSlugModal').classList.add('hidden')"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200">{{ __('Cancel') }}</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function forceRefreshPlayer() {
            fetch('{{ route('admin.channels.refresh') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
            }).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('Refresh command sent to player screens.') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        }
    </script>

    <!-- Create Channel Modal -->
    <div id="createChannelModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('Create New Channel') }}</h3>
            <form action="{{ route('admin.channels.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Channel Name') }}</label>
                    <input type="text" name="name" id="createChannelName" required
                        placeholder="{{ __('e.g. Ruang Tunggu Poli A') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500 mb-3"
                        oninput="document.getElementById('createChannelSlug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')">

                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Slug') }}</label>
                    <input type="text" name="slug" id="createChannelSlug"
                        placeholder="{{ __('auto-generated-from-name') }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500 mb-1">
                    <p class="text-xs text-gray-500 mb-3">
                        {{ __('Leave empty to auto-generate from name. Only lowercase letters, numbers, and hyphens.') }}
                    </p>

                    <input type="hidden" name="orientation" value="0">
                </div>
                <!-- optional: default videos? For now, just generate the channel -->
                <div class="flex justify-end gap-2">
                    <button type="button"
                        onclick="document.getElementById('createChannelModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200">{{ __('Cancel') }}</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">{{ __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Help Modal -->
    <div id="helpModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6 border-b pb-2">
                <h3 class="text-xl font-extrabold text-gray-800">
                    {{ __('Panduan Penggunaan Aplikasi (Digital Signage / TV Information)') }}</h3>
                <button type="button" onclick="document.getElementById('helpModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-6 text-sm text-gray-700">

                <!-- 1. Konsep Dasar -->
                <div>
                    <h4 class="font-bold text-lg text-orange-600 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('1. Konsep Dasar') }}
                    </h4>
                    <div class="pl-7 space-y-2">
                        <p>{{ __('Aplikasi ini berfungsi sebagai sistem kontrol terpusat untuk menampilkan informasi (video, gambar, teks berjalan, jadwal) ke satu atau lebih layar TV.') }}
                        </p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Admin Panel:</strong> {{ __('Layar ini tempat Anda mengatur semua konten.') }}
                            </li>
                            <li><strong>Player Screen:</strong>
                                {{ __('Layar hasil akhir yang ditampilkan di TV. Biarkan layar ini terbuka di browser TV / Smart TV Anda.') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 2. Mengelola Saluran -->
                <div>
                    <h4 class="font-bold text-lg text-orange-600 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        {{ __('2. Mengelola Saluran (Channel)') }}
                    </h4>
                    <div class="pl-7 space-y-2">
                        <p>{{ __('Setiap "Channel" mewakili satu TV atau lokasi (misal: "Ruang Tunggu A", "Lobi Utama"). Konten di setiap channel terpisah dan tidak saling mengganggu.') }}
                        </p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Memilih Channel:</strong>
                                {{ __('Gunakan dropdown di bagian atas layar Admin untuk berpindah ruang kerja antar channel.') }}
                            </li>
                            <li><strong>Membuat Channel Baru:</strong>
                                {{ __('Klik tombol "New" di samping dropdown. Masukkan nama channel (contoh: Poli Gigi).') }}
                            </li>
                            <li><strong>Membuka TV:</strong>
                                {{ __('Klik tombol "Player Screen" di kanan atas. Ini akan membuka tab baru sesuai channel yang sedang aktif. Hubungkan tab tersebut ke layar TV yang dituju.') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 3. Perpustakaan Media (Video) -->
                <div>
                    <h4 class="font-bold text-lg text-orange-600 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ __('3. Mengatur Video (Media Library)') }}
                    </h4>
                    <div class="pl-7 space-y-2">
                        <p>{{ __('Menu ini adalah pusat pengaturan video yang akan berputar di Player.') }}</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Unggah Video:</strong>
                                {{ __('Klik tombol "Upload New" untuk menambahkan video (.mp4) ke sistem.') }}</li>
                            <li><strong>Memutar Video:</strong>
                                {{ __('Video yang baru diunggah hanya tersimpan di perpustakaan. Untuk memutarnya di TV, seret (drag) video dari "Library" (kiri) ke dalam kotak "Playlist" (kanan).') }}
                            </li>
                            <li><strong>Mengatur Urutan:</strong>
                                {{ __('Seret video di dalam "Playlist" ke atas atau ke bawah untuk mengatur urutan putarnya.') }}
                            </li>
                            <li><strong>Menghapus Video dari Playlist:</strong>
                                {{ __('Klik tombol merah (Remove) pada video di Playlist.') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- 4. Slide Gambar -->
                <div>
                    <h4 class="font-bold text-lg text-orange-600 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ __('4. Slide Gambar (Image Slides)') }}
                    </h4>
                    <div class="pl-7 space-y-2">
                        <p>{{ __('Digunakan untuk menampilkan gambar promosi, poster, atau pengumuman visual secara bergantian.') }}
                        </p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Upload Gambar:</strong>
                                {{ __('Klik "Upload New Slide". Anda dapat mengatur durasi berapa detik gambar tersebut akan tampil di layar.') }}
                            </li>
                            <li><strong>Penempatan:</strong>
                                {{ __('Gambar akan muncul di layar Player sebagai latar belakang (jika tidak ada video) atau mengisi ruang kosong di layar sesuai layout yang aktif.') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 5. Jadwal Acara / Pengumuman -->
                <div>
                    <h4 class="font-bold text-lg text-orange-600 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ __('5. Jadwal Acara (Event Schedules)') }}
                    </h4>
                    <div class="pl-7 space-y-2">
                        <p>{{ __('Menu ini digunakan untuk menampilkan daftar agenda atau informasi statis di samping layar Player.') }}
                        </p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>{{ __('Sangat berguna untuk klinik (menampilkan daftar dokter jaga hari ini) atau ruang rapat (menampilkan jadwal meeting).') }}
                            </li>
                            <li>{{ __('Gunakan tombol "New Schedule" untuk menambahkan teks baru, atur posisinya dengan menyeret ke atas/bawah, lalu aktifkan toggle (switch) agar tampil di TV.') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 6. Alat & Preferensi -->
                <div>
                    <h4 class="font-bold text-lg text-orange-600 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ __('6. Alat & Preferensi (Tools/Prefs)') }}
                    </h4>
                    <div class="pl-7 space-y-2">
                        <p>{{ __('Pengaturan tampilan utama untuk TV Anda.') }}</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Running Text:</strong> {{ __('Ubah teks yang berjalan di bagian bawah TV.') }}
                            </li>
                            <li><strong>Logo & Branding:</strong>
                                {{ __('Unggah logo perusahaan/instansi agar tampil di sudut TV.') }}</li>
                            <li><strong>Refresh TV (Tombol di Atas):</strong>
                                {{ __('Jika TV terasa macet atau tidak sinkron, klik tombol "Refresh TV" di menu atas untuk memaksa TV me-reload secara otomatis.') }}
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="flex justify-end gap-2 mt-8 pt-4 border-t border-gray-200">
                <button type="button" onclick="document.getElementById('helpModal').classList.add('hidden')"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-orange-600 rounded-md shadow hover:bg-orange-700 transition-colors">{{ __('Tutup Panduan') }}</button>
            </div>
        </div>
    </div>
</body>

</html>
