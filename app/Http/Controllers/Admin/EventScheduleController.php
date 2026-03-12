<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventScheduleRequest;
use App\Models\Channel;
use App\Models\EventSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = EventSchedule::query()
            ->with('channels')
            ->orderBy('starts_at')
            ->get();

        return view('admin.event-schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        $channels = Channel::query()->orderBy('name')->get();

        return view('admin.event-schedules.form', compact('channels'));
    }

    public function store(EventScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $channelIds = $data['channel_ids'];
        unset($data['channel_ids']);

        $schedule = EventSchedule::query()->create($data);
        $schedule->channels()->sync($channelIds);

        return redirect()->route('admin.event-schedules.index')
            ->with('success', __('Event schedule created.'));
    }

    public function edit(EventSchedule $eventSchedule): View
    {
        $channels = Channel::query()->orderBy('name')->get();
        $selectedChannelIds = $eventSchedule->channels->pluck('id')->all();

        return view('admin.event-schedules.form', compact('eventSchedule', 'channels', 'selectedChannelIds'));
    }

    public function update(EventScheduleRequest $request, EventSchedule $eventSchedule): RedirectResponse
    {
        $data = $request->validated();
        $channelIds = $data['channel_ids'];
        unset($data['channel_ids']);

        $eventSchedule->update($data);
        $eventSchedule->channels()->sync($channelIds);

        return redirect()->route('admin.event-schedules.index')
            ->with('success', __('Event schedule updated.'));
    }

    public function destroy(EventSchedule $eventSchedule): RedirectResponse
    {
        $eventSchedule->delete();

        return redirect()->route('admin.event-schedules.index')
            ->with('success', __('Event schedule deleted.'));
    }
}
