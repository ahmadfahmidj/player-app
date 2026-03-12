@extends('layouts.admin')
@section('title', __('Event Schedules'))

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-6xl mx-auto space-y-4">

        <div
            class="flex flex-col md:flex-row justify-between items-center bg-[#f0f0f0] border border-gray-400 px-4 py-3 rounded shadow shadow-gray-400/20">
            <div>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 shadow-sm">
                    <img src="{{ asset('favicon.svg') }}" class="w-5 h-5" alt="Logo">
                    {{ __('Event Schedules') }}
                </h1>
            </div>
            <a href="{{ route('admin.event-schedules.create') }}"
                class="mt-2 md:mt-0 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold uppercase tracking-wider px-4 py-2 rounded shadow transition-colors">
                + {{ __('New Schedule') }}
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 text-sm px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-100 border border-gray-400 rounded shadow-md overflow-hidden">
            @if ($schedules->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <p class="text-sm">{{ __('No event schedules yet.') }}</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200 border-b border-gray-400">
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2 align-middle">
                                {{ __('Event') }}</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2 align-middle">
                                {{ __('Start') }}</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2 align-middle">
                                {{ __('End') }}</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2 align-middle">
                                {{ __('Channels') }}</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2 align-middle">
                                {{ __('Status') }}</th>
                            <th class="px-4 py-2 align-middle"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @foreach ($schedules as $schedule)
                            @php
                                $now = now();
                                $isRunning = $schedule->starts_at <= $now && $schedule->ends_at >= $now;
                                $isUpcoming =
                                    !$isRunning &&
                                    $schedule->starts_at > $now &&
                                    $schedule->starts_at <= $now->copy()->addMinutes(60);
                                $isPast = $schedule->ends_at < $now;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-bold text-gray-800">{{ $schedule->title }}</div>
                                    @if ($schedule->location)
                                        <div class="text-xs text-gray-500">{{ $schedule->location }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle text-gray-700 text-xs">{{ $schedule->starts_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 align-middle text-gray-700 text-xs">{{ $schedule->ends_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($schedule->channels as $channel)
                                            <span
                                                class="bg-blue-100 text-blue-700 border border-blue-200 text-xs px-2 py-0.5 rounded font-medium">
                                                {{ $channel->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    @if ($isRunning)
                                        <span
                                            class="bg-green-100 text-green-700 border border-green-300 text-xs font-bold px-2 py-0.5 rounded">
                                            {{ __('LIVE') }}
                                        </span>
                                    @elseif ($isUpcoming)
                                        <span
                                            class="bg-yellow-100 text-yellow-700 border border-yellow-300 text-xs font-bold px-2 py-0.5 rounded">
                                            {{ __('SOON') }}
                                        </span>
                                    @elseif ($isPast)
                                        <span
                                            class="bg-gray-100 text-gray-500 border border-gray-300 text-xs px-2 py-0.5 rounded">
                                            {{ __('past') }}
                                        </span>
                                    @else
                                        <span
                                            class="bg-blue-50 text-blue-600 border border-blue-200 text-xs px-2 py-0.5 rounded">
                                            {{ __('scheduled') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.event-schedules.edit', $schedule) }}"
                                            class="text-gray-500 hover:text-orange-600 transition-colors"
                                            title="{{ __('Edit') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828z" />
                                                <path fill-rule="evenodd"
                                                    d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.event-schedules.destroy', $schedule) }}"
                                            method="POST"
                                            onsubmit="event.preventDefault(); Swal.fire({ title: '{{ __('Delete this schedule?') }}', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: '{{ __('Yes, delete it!') }}', cancelButtonText: '{{ __('Cancel') }}' }).then((result) => { if (result.isConfirmed) this.submit(); });">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors"
                                                title="{{ __('Delete') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
@endsection
