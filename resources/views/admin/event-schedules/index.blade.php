@extends('layouts.admin')
@section('title', 'Event Schedules')

@section('content')
    <div class="animate-in fade-in duration-300 w-full max-w-6xl mx-auto space-y-4">

        <div class="flex flex-col md:flex-row justify-between items-center bg-[#f0f0f0] border border-gray-400 px-4 py-3 rounded shadow shadow-gray-400/20">
            <div>
                <h1 class="text-lg font-bold text-gray-800 flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L2 22H22L12 2Z" />
                    </svg>
                    Event Schedules
                </h1>
            </div>
            <a href="{{ route('admin.event-schedules.create') }}"
                class="mt-2 md:mt-0 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold uppercase tracking-wider px-4 py-2 rounded shadow transition-colors">
                + New Schedule
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
                    <p class="text-sm">No event schedules yet.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200 border-b border-gray-400">
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2">Event</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2">Start</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2">End</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2">Channels</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-gray-600 px-4 py-2">Status</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @foreach ($schedules as $schedule)
                            @php
                                $now = now();
                                $isRunning = $schedule->starts_at <= $now && $schedule->ends_at >= $now;
                                $isUpcoming = ! $isRunning && $schedule->starts_at > $now && $schedule->starts_at <= $now->copy()->addMinutes(60);
                                $isPast = $schedule->ends_at < $now;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800">{{ $schedule->title }}</div>
                                    @if ($schedule->location)
                                        <div class="text-xs text-gray-500">{{ $schedule->location }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700 text-xs">{{ $schedule->starts_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-gray-700 text-xs">{{ $schedule->ends_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($schedule->channels as $channel)
                                            <span class="bg-blue-100 text-blue-700 border border-blue-200 text-xs px-2 py-0.5 rounded font-medium">
                                                {{ $channel->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($isRunning)
                                        <span class="bg-green-100 text-green-700 border border-green-300 text-xs font-bold px-2 py-0.5 rounded">
                                            LIVE
                                        </span>
                                    @elseif ($isUpcoming)
                                        <span class="bg-yellow-100 text-yellow-700 border border-yellow-300 text-xs font-bold px-2 py-0.5 rounded">
                                            SOON
                                        </span>
                                    @elseif ($isPast)
                                        <span class="bg-gray-100 text-gray-500 border border-gray-300 text-xs px-2 py-0.5 rounded">
                                            past
                                        </span>
                                    @else
                                        <span class="bg-blue-50 text-blue-600 border border-blue-200 text-xs px-2 py-0.5 rounded">
                                            scheduled
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.event-schedules.edit', $schedule) }}"
                                            class="text-xs font-bold text-gray-600 hover:text-orange-600 uppercase tracking-wider transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.event-schedules.destroy', $schedule) }}" method="POST"
                                            onsubmit="return confirm('Delete this schedule?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-bold text-red-500 hover:text-red-700 uppercase tracking-wider transition-colors">
                                                Delete
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
