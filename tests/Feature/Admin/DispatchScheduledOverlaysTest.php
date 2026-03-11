<?php

use App\Events\EventOverlayUpdated;
use App\Models\Channel;
use App\Models\EventSchedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->channel = Channel::where('is_main', true)->first() ?? Channel::factory()->create(['is_main' => true, 'slug' => 'main']);
    Cache::flush();
});

test('overlay:dispatch command exists and runs successfully', function () {
    $this->artisan('overlay:dispatch')->assertSuccessful();
});

test('dispatches overlay for currently running event', function () {
    Event::fake([EventOverlayUpdated::class]);

    $schedule = EventSchedule::factory()->currentlyRunning()->create([
        'title' => 'Running Event',
        'location' => 'Hall A',
        'organizer' => 'Panitia',
    ]);
    $schedule->channels()->attach($this->channel);

    $this->artisan('overlay:dispatch');

    Event::assertDispatched(EventOverlayUpdated::class, function ($event) {
        return $event->overlayData['show'] === true
            && $event->overlayData['title'] === 'Running Event'
            && $event->channelSlug === $this->channel->slug;
    });
});

test('dispatches overlay for upcoming event within 60 minutes', function () {
    Event::fake([EventOverlayUpdated::class]);

    $schedule = EventSchedule::factory()->upcomingIn(30)->create([
        'title' => 'Upcoming Event',
    ]);
    $schedule->channels()->attach($this->channel);

    $this->artisan('overlay:dispatch');

    Event::assertDispatched(EventOverlayUpdated::class, function ($event) {
        return $event->overlayData['show'] === true
            && $event->overlayData['title'] === 'Upcoming Event';
    });
});

test('does not dispatch overlay for event starting more than 60 minutes away', function () {
    Event::fake([EventOverlayUpdated::class]);

    $schedule = EventSchedule::factory()->upcomingIn(90)->create(['title' => 'Far Future Event']);
    $schedule->channels()->attach($this->channel);

    $this->artisan('overlay:dispatch');

    Event::assertDispatched(EventOverlayUpdated::class, function ($event) {
        return $event->overlayData['show'] === false;
    });
});

test('dispatches hide overlay when no active or upcoming events', function () {
    Event::fake([EventOverlayUpdated::class]);

    $schedule = EventSchedule::factory()->past()->create();
    $schedule->channels()->attach($this->channel);

    $this->artisan('overlay:dispatch');

    Event::assertDispatched(EventOverlayUpdated::class, function ($event) {
        return $event->overlayData['show'] === false
            && $event->channelSlug === $this->channel->slug;
    });
});

test('prefers currently running event over upcoming event', function () {
    Event::fake([EventOverlayUpdated::class]);

    $running = EventSchedule::factory()->currentlyRunning()->create(['title' => 'Running Now']);
    $running->channels()->attach($this->channel);

    $upcoming = EventSchedule::factory()->upcomingIn(30)->create(['title' => 'Coming Soon']);
    $upcoming->channels()->attach($this->channel);

    $this->artisan('overlay:dispatch');

    Event::assertDispatched(EventOverlayUpdated::class, function ($event) {
        return $event->overlayData['title'] === 'Running Now';
    });
});

test('does not re-dispatch if overlay state has not changed', function () {
    Event::fake([EventOverlayUpdated::class]);

    $schedule = EventSchedule::factory()->currentlyRunning()->create(['title' => 'Stable Event']);
    $schedule->channels()->attach($this->channel);

    $this->artisan('overlay:dispatch');
    $this->artisan('overlay:dispatch');

    Event::assertDispatchedTimes(EventOverlayUpdated::class, 1);
});

test('only affects channels that have the event schedule assigned', function () {
    Event::fake([EventOverlayUpdated::class]);

    $otherChannel = Channel::factory()->create(['slug' => 'other', 'is_main' => false]);

    $schedule = EventSchedule::factory()->currentlyRunning()->create(['title' => 'Channel-specific Event']);
    $schedule->channels()->attach($this->channel);

    $this->artisan('overlay:dispatch');

    Event::assertDispatched(EventOverlayUpdated::class, function ($event) {
        return $event->channelSlug === $this->channel->slug
            && $event->overlayData['show'] === true;
    });

    Event::assertDispatched(EventOverlayUpdated::class, function ($event) {
        return $event->channelSlug === 'other'
            && $event->overlayData['show'] === false;
    });
});
