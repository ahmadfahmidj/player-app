@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-4xl mx-auto space-y-4">

        <div
            class="flex flex-col md:flex-row justify-between items-center bg-[#f0f0f0] border border-gray-400 px-4 py-3 rounded shadow shadow-gray-400/20">
            <div>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L2 22H22L12 2Z" />
                    </svg>
                    Preferences
                </h1>
            </div>
        </div>

        {{-- Screen Orientation --}}
        <div class="bg-gray-100 border border-gray-400 rounded shadow-md p-4">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-gray-300 pb-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6h16v12H4z" />
                </svg>
                Screen Orientation
            </h2>

            <form action="{{ route('admin.settings.screen-orientation') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-2">Display Mode</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <select name="screen_orientation" class="flex-1 bg-white border border-gray-400 text-gray-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-500 shadow-inner">
                            <option value="landscape" {{ $screenOrientation === 'landscape' ? 'selected' : '' }}>Landscape</option>
                            <option value="portrait" {{ $screenOrientation === 'portrait' ? 'selected' : '' }}>Portrait (Rotated 90°)</option>
                        </select>
                        <button type="submit"
                            class="shrink-0 px-6 py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 shadow text-gray-800 font-bold active:bg-gray-400 text-xs rounded uppercase tracking-wider transition-all flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save
                        </button>
                    </div>
                    @error('screen_orientation')
                        <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        {{-- Running Text --}}
        <div class="bg-gray-100 border border-gray-400 rounded shadow-md p-4">
            <h2
                class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-gray-300 pb-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                    </path>
                </svg>
                OSD Overlay (Ticker)
            </h2>

            <form action="{{ route('admin.settings.running-text') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-2">Message
                        String</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="text" value="{{ $runningText }}" required
                            class="flex-1 bg-white border border-gray-400 text-gray-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-500 shadow-inner @error('text') border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 @enderror"
                            placeholder="Enter the message to scroll across the OSD...">
                        <button type="submit"
                            class="shrink-0 px-6 py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 shadow text-gray-800 font-bold active:bg-gray-400 text-xs rounded uppercase tracking-wider transition-all flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Save
                        </button>
                    </div>
                    @error('text')
                        <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        {{-- Logo --}}
        <div class="bg-[#f0f0f0] border border-gray-400 rounded shadow-md p-4">
            <h2
                class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-gray-300 pb-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0V20a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm.5 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z">
                    </path>
                </svg>
                Display Icon (Logo)
            </h2>

            <div class="flex flex-col md:flex-row gap-6 relative z-10">
                @if ($logoUrl)
                    <div class="shrink-0 w-full md:w-64">
                        <div class="flex items-center justify-between mb-2">
                            <p class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider">Current Asset</p>
                            <form id="delete-logo-form" action="{{ route('admin.settings.logo.destroy') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="Swal.fire({ title: 'Remove this logo?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, remove it' }).then((result) => { if (result.isConfirmed) document.getElementById('delete-logo-form').submit(); })"
                                    class="text-[10px] font-bold text-red-600 uppercase tracking-wider hover:text-red-700 transition-colors flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Remove
                                </button>
                            </form>
                        </div>
                        <div
                            class="bg-gray-200 border border-gray-400 rounded p-4 flex flex-col items-center justify-center min-h-[140px] shadow-inner relative">
                            <img src="{{ $logoUrl }}" alt="Hospital Logo"
                                class="max-h-20 max-w-full object-contain filter drop-shadow">
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data"
                    class="flex-grow space-y-3 flex flex-col justify-end">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-2">Upload
                            Asset</label>
                        <div class="relative w-full">
                            <input type="file" name="logo" accept=".png,.svg,.jpg,.jpeg" required id="logo-input"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div
                                class="w-full text-xs px-3 py-6 border border-dashed border-gray-400 shadow-sm rounded bg-white text-gray-600 text-center flex flex-col items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span id="logo-label-text" class="font-bold">Select image file...</span>
                                <span class="text-[10px] text-gray-500 font-mono">PNG, SVG, JPG</span>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full relative overflow-hidden px-6 py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 shadow text-gray-800 font-bold active:bg-gray-400 text-xs rounded uppercase tracking-wider transition-all flex justify-center items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Apply Asset
                    </button>
                </form>
            </div>
        </div>
        {{-- Event Schedule Overlay --}}
        <div class="bg-[#f0f0f0] border border-gray-400 rounded shadow-md p-4 mb-4">
            <h2
                class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center justify-between border-b border-gray-300 pb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5z"/>
                    </svg>
                    Event Schedule Overlay
                </span>
            </h2>

            <form action="{{ route('admin.settings.event-overlay') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Toggle Show/Hide --}}
                <div class="flex items-center gap-3 bg-white p-3 rounded border border-gray-300 shadow-sm">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="overlay_show" class="sr-only peer" {{ $overlayShow == '1' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-bold text-gray-700">Display Overlay on TV</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Location --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Building / Location</label>
                        <input type="text" name="overlay_location" value="{{ $overlayLocation }}"
                            class="w-full bg-white border border-gray-400 text-gray-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-500 shadow-inner"
                            placeholder="e.g. AULA SOEDIRMAN">
                    </div>

                    {{-- Subtitle --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Subtitle</label>
                        <input type="text" name="overlay_subtitle" value="{{ $overlaySubtitle }}"
                            class="w-full bg-white border border-gray-400 text-gray-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-500 shadow-inner"
                            placeholder="e.g. ACARA BERIKUTNYA">
                    </div>
                </div>

                {{-- Event Title --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Main Event Title</label>
                    <input type="text" name="overlay_title" value="{{ $overlayTitle }}"
                        class="w-full bg-white border border-gray-400 text-gray-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-500 shadow-inner font-bold"
                        placeholder="e.g. SEMINAR KESELAMATAN PASIEN">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Time --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Time Range</label>
                        <input type="text" name="overlay_time" value="{{ $overlayTime }}"
                            class="w-full bg-white border border-gray-400 text-gray-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-500 shadow-inner"
                            placeholder="e.g. 08:00 - 12:00">
                    </div>

                    {{-- Organizer --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Organizer / PIC</label>
                        <textarea name="overlay_organizer" rows="2"
                            class="w-full bg-white border border-gray-400 text-gray-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-500 shadow-inner resize-none"
                            placeholder="e.g. BIDANG DIKLAT RSUD... PIC: Dr. Budi...">{{ $overlayOrganizer }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 border border-gray-400 shadow text-gray-800 font-bold active:bg-gray-400 text-xs rounded uppercase tracking-wider transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Overlay Details
                    </button>
                </div>
            </form>
        </div>

    </div>
    <script>
        document.getElementById('logo-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : "Select image file...";
            document.getElementById('logo-label-text').innerText = fileName;
        });
    </script>
@endsection
