@extends('layouts.admin')
@section('title', isset($eventSchedule) ? __('Edit Event Schedule') : __('New Event Schedule'))

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-2xl mx-auto space-y-4">

        <div class="flex items-center bg-[#f0f0f0] border border-gray-400 px-4 py-3 rounded shadow shadow-gray-400/20">
            <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 shadow-sm">
                <img src="{{ asset('favicon.svg') }}" class="w-5 h-5" alt="Logo">
                {{ isset($eventSchedule) ? __('Edit Event Schedule') : __('New Event Schedule') }}
            </h1>
        </div>

        <div class="bg-gray-100 border border-gray-400 rounded shadow-md p-6">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 text-sm px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ isset($eventSchedule) ? route('admin.event-schedules.update', $eventSchedule) : route('admin.event-schedules.store') }}"
                method="POST" class="space-y-4">
                @csrf
                @if (isset($eventSchedule))
                    @method('PUT')
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                        {{ __('Title') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $eventSchedule->title ?? '') }}"
                        class="w-full border border-gray-300 bg-white rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-400 shadow-inner"
                        required maxlength="200" placeholder="{{ __('Event title') }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">{{ __('Location') }}</label>
                        <input type="text" name="location"
                            value="{{ old('location', $eventSchedule->location ?? '') }}"
                            class="w-full border border-gray-300 bg-white rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-400 shadow-inner"
                            maxlength="100" placeholder="{{ __('e.g. Gedung Serbaguna') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">{{ __('Subtitle') }}</label>
                        <input type="text" name="subtitle"
                            value="{{ old('subtitle', $eventSchedule->subtitle ?? '') }}"
                            class="w-full border border-gray-300 bg-white rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-400 shadow-inner"
                            maxlength="100" placeholder="{{ __('e.g. Sesi Pleno') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">{{ __('Time Display') }}</label>
                        <input type="text" name="time_display"
                            value="{{ old('time_display', $eventSchedule->time_display ?? '') }}"
                            class="w-full border border-gray-300 bg-white rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-400 shadow-inner"
                            maxlength="100" placeholder="{{ __('e.g. 08:00 - 10:00 WIB') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">{{ __('Organizer') }}</label>
                        <input type="text" name="organizer"
                            value="{{ old('organizer', $eventSchedule->organizer ?? '') }}"
                            class="w-full border border-gray-300 bg-white rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-400 shadow-inner"
                            maxlength="200" placeholder="{{ __('Organizing committee name') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                            {{ __('Start Date & Time') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="starts_at"
                            value="{{ old('starts_at', isset($eventSchedule) ? $eventSchedule->starts_at->format('Y-m-d\TH:i') : '') }}"
                            class="w-full border border-gray-300 bg-white rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-400 shadow-inner"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                            {{ __('End Date & Time') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="ends_at"
                            value="{{ old('ends_at', isset($eventSchedule) ? $eventSchedule->ends_at->format('Y-m-d\TH:i') : '') }}"
                            class="w-full border border-gray-300 bg-white rounded px-3 py-2 text-sm focus:outline-none focus:border-orange-400 shadow-inner"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">
                        {{ __('Channels') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        @foreach ($channels as $channel)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="channel_ids[]" value="{{ $channel->id }}"
                                    {{ in_array($channel->id, old('channel_ids', $selectedChannelIds ?? [])) ? 'checked' : '' }}
                                    class="rounded border-gray-400 text-orange-500 focus:ring-orange-400">
                                <span class="text-sm text-gray-700">
                                    {{ $channel->name }}
                                    @if ($channel->is_main)
                                        <span class="text-xs text-gray-400">({{ __('main') }})</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-300">
                    <a href="{{ route('admin.event-schedules.index') }}"
                        class="text-xs font-bold text-gray-500 hover:text-gray-700 uppercase tracking-wider transition-colors">
                        &larr; {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold uppercase tracking-wider px-6 py-2 rounded shadow transition-colors">
                        {{ isset($eventSchedule) ? __('Update Schedule') : __('Create Schedule') }}
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection
