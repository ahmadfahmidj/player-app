<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Hospital TV</title>
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
        <x-instruckt-toolbar />
        <div class="bg-gray-100 flex items-center justify-between px-4 py-2 border-b border-gray-300">
            <div class="flex items-center gap-4">
                <div class="flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500 border border-red-600"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-400 border border-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500 border border-green-600"></div>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-800">
                    <div class="w-4 h-4 text-orange-500 hidden md:block">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 22H22L12 2Z" />
                        </svg>
                    </div>
                    Mainan TV Admin
                </div>
                <div class="flex items-center gap-4">
                    <form action="{{ route('admin.channels.switch') }}" method="POST" class="flex items-center">
                        @csrf
                        <select name="channel_id" onchange="this.form.submit()"
                            class="text-xs bg-white border-2 border-orange-400 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-[0_2px_4px_rgba(255,136,0,0.1)] font-bold text-gray-900 cursor-pointer hover:border-orange-500 transition-all">
                            @foreach ($allChannels as $c)
                                <option value="{{ $c->id }}"
                                    {{ $c->id == ($activeChannel->id ?? 0) ? 'selected' : '' }}>
                                    {{ $c->name }} {{ $c->is_main ? '(Main)' : '' }}
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
                            </svg> New</button>
                        @if (isset($activeChannel) && !$activeChannel->is_main)
                            <form action="{{ route('admin.channels.destroy', $activeChannel) }}" method="POST"
                                onsubmit="event.preventDefault(); Swal.fire({ title: 'Delete this channel?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!' }).then((result) => { if (result.isConfirmed) this.submit(); });">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xs px-2 py-1 bg-gray-200 border border-gray-400 hover:bg-gray-300 text-red-600 font-bold rounded shadow-sm transition-all flex items-center gap-1 leading-none"><svg
                                        class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg> Del</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-row items-center gap-4">
                <a href="{{ isset($activeChannel) && !$activeChannel->is_main ? route('player.channel', $activeChannel->slug) : route('player') }}"
                    target="_blank"
                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1 bg-gray-200 text-gray-700 rounded border border-gray-400 hover:bg-gray-300 transition-all shadow-sm">
                    Player Screen
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button type="submit"
                        class="text-xs font-medium text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 bg-gray-200 border border-gray-400 px-2 py-1 rounded shadow-sm hover:bg-white">
                        Quit
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
                    Dashboard</a>
                <a href="{{ route('admin.videos') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.videos') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                    </svg>
                    Media Library</a>
                <a href="{{ route('admin.image-slides') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.image-slides') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Image Slides</a>
                <a href="{{ route('admin.event-schedules.index') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.event-schedules.*') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Event Schedules</a>
                <a href="{{ route('admin.settings') }}"
                    class="text-xs text-gray-800 cursor-pointer hover:bg-gray-300 px-2 py-1.5 rounded flex items-center gap-1.5 {{ request()->routeIs('admin.settings') ? 'bg-gray-300 font-bold' : '' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Tools/Prefs</a>
                <span class="text-xs text-gray-400 cursor-not-allowed px-2 py-1.5 rounded flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View</span>
                <span class="text-xs text-gray-400 cursor-not-allowed px-2 py-1.5 rounded flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Help</span>
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

    <!-- Create Channel Modal -->
    <div id="createChannelModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Create New Channel</h3>
            <form action="{{ route('admin.channels.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Channel Name</label>
                    <input type="text" name="name" required placeholder="e.g. Ruang Tunggu Poli A"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500 mb-3">

                    <label class="block text-sm font-medium text-gray-700 mb-1">Screen Rotation (Orientation)</label>
                    <select name="orientation"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        disabled>
                        <option value="0">0° (Normal)</option>
                        <option value="90">90° (Clockwise)</option>
                        <option value="180">180° (Upside Down)</option>
                        <option value="270">270° (Counter-clockwise)</option>
                    </select>
                </div>
                <!-- optional: default videos? For now, just generate the channel -->
                <div class="flex justify-end gap-2">
                    <button type="button"
                        onclick="document.getElementById('createChannelModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Create</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
