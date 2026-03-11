<?php

namespace App\Console\Commands;

use App\Events\EventOverlayUpdated;
use App\Models\Channel;
use App\Models\EventSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DispatchScheduledOverlays extends Command
{
    protected $signature = 'overlay:dispatch';

    protected $description = 'Dispatch scheduled overlay broadcasts to channels based on active event schedules';

    public function handle(): int
    {
        Channel::query()->each(function (Channel $channel) {
            $activeEvent = EventSchedule::query()
                ->currentlyRunning()
                ->whereHas('channels', fn ($q) => $q->where('channels.id', $channel->id))
                ->first();

            if (! $activeEvent) {
                $activeEvent = EventSchedule::query()
                    ->upcomingWithin(60)
                    ->whereHas('channels', fn ($q) => $q->where('channels.id', $channel->id))
                    ->orderBy('starts_at')
                    ->first();
            }

            $overlayData = $activeEvent
                ? [
                    'show' => true,
                    'location' => $activeEvent->location,
                    'subtitle' => $activeEvent->subtitle,
                    'title' => $activeEvent->title,
                    'time' => $activeEvent->time_display,
                    'organizer' => $activeEvent->organizer,
                ]
                : [
                    'show' => false,
                    'location' => '',
                    'subtitle' => '',
                    'title' => '',
                    'time' => '',
                    'organizer' => '',
                ];

            $cacheKey = "overlay_state_{$channel->id}";
            $previousData = Cache::get($cacheKey);

            if ($previousData !== $overlayData) {
                EventOverlayUpdated::dispatch($overlayData, $channel->slug);
                Cache::put($cacheKey, $overlayData, now()->addMinutes(5));
            }
        });

        return self::SUCCESS;
    }
}
