@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
    <div class="space-y-8 animate-in fade-in zoom-in-95 duration-500 max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1
                    class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300 tracking-tight">
                    System Settings</h1>
                <p class="text-slate-400 mt-1">Configure broadcast overrides and appearance</p>
            </div>
        </div>

        {{-- Running Text --}}
        <div
            class="rounded-2xl bg-slate-800/50 backdrop-blur-xl border border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-6 md:p-8 relative overflow-hidden group">
            <div
                class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
            </div>
            <h2
                class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6 flex items-center gap-2 relative z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                    </path>
                </svg>
                Running Text (Ticker)
            </h2>

            <form action="{{ route('admin.settings.running-text') }}" method="POST" class="space-y-4 relative z-10">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Message
                        Content</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="text" value="{{ $runningText }}" required
                            class="flex-1 bg-slate-900/50 border border-slate-700 text-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-inner @error('text') border-rose-500/50 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            placeholder="Enter the message to scroll across the bottom of the screen...">
                        <button type="submit"
                            class="shrink-0 relative overflow-hidden group/btn px-8 py-3 bg-blue-600 hover:bg-blue-500 border-none text-white rounded-xl font-bold text-sm transition-all duration-300 shadow-[0_0_20px_rgba(37,99,235,0.2)] hover:shadow-[0_0_30px_rgba(37,99,235,0.4)] flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Update Ticker
                        </button>
                    </div>
                    @error('text')
                        <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        {{-- Logo --}}
        <div
            class="rounded-2xl bg-slate-800/50 backdrop-blur-xl border border-white/10 shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-6 md:p-8 relative overflow-hidden group">
            <div
                class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-pink-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
            </div>
            <h2
                class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-6 flex items-center gap-2 relative z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0V20a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm.5 0a1.5 1.5 0 100-3 1.5 1.5 0 000 3z">
                    </path>
                </svg>
                Hospital Logo
            </h2>

            <div class="flex flex-col md:flex-row gap-8 relative z-10">
                @if ($logoUrl)
                    <div class="shrink-0 w-full md:w-64">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Current Logo</p>
                        <div
                            class="bg-slate-900/80 border border-slate-700/50 rounded-2xl p-6 flex flex-col items-center justify-center min-h-[160px] relative group/logo">
                            <div
                                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPjpyZWN0IHdpZHRoPSI4IiBoZWlnaHQ9IjgiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjxwYXRoIGQ9Ik0wIDBMOCA4Wk04IDBMMCA4WiIgc3Ryb2tlPSIjZmZmIiBzdHJva2Utb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-20 rounded-2xl">
                            </div>
                            <img src="{{ $logoUrl }}" alt="Hospital Logo"
                                class="max-h-24 max-w-full object-contain relative z-10 filter drop-shadow-[0_4px_8px_rgba(0,0,0,0.5)] transition-transform duration-500 group-hover/logo:scale-110">
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data"
                    class="flex-grow space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Upload New
                            Logo</label>
                        <div class="relative group/file mb-4">
                            <input type="file" name="logo" accept=".png,.svg,.jpg,.jpeg" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div
                                class="bg-slate-900/50 border-2 border-dashed border-slate-700 group-hover/file:border-indigo-500/50 rounded-xl px-4 py-8 text-center transition-colors">
                                <svg class="w-8 h-8 mx-auto text-slate-500 mb-2 group-hover/file:text-indigo-400 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span class="text-sm font-medium text-slate-300 block">Click or drag image file here</span>
                                <span class="text-xs text-slate-500 font-mono mt-1 block">Supports PNG, SVG, JPG</span>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-2 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full relative overflow-hidden group/btn px-6 py-3 bg-indigo-600 hover:bg-indigo-500 border-none text-white rounded-xl font-bold text-sm transition-all duration-300 shadow-[0_0_20px_rgba(79,70,229,0.2)] hover:shadow-[0_0_30px_rgba(79,70,229,0.4)] flex justify-center items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Upload and Apply
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
