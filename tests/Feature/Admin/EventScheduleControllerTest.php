<?php

use App\Models\Channel;
use App\Models\EventSchedule;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->channel = Channel::where('is_main', true)->first() ?? Channel::factory()->create(['is_main' => true, 'slug' => 'main']);
    session(['active_channel_id' => $this->channel->id]);
});

test('guests cannot access event schedules', function () {
    auth()->logout();

    $this->get(route('admin.event-schedules.index'))->assertRedirect(route('login'));
    $this->get(route('admin.event-schedules.create'))->assertRedirect(route('login'));
});

test('admin can view event schedules index', function () {
    EventSchedule::factory()->currentlyRunning()->create(['title' => 'Test Seminar'])
        ->channels()->attach($this->channel);

    $this->withoutVite()->get(route('admin.event-schedules.index'))
        ->assertOk()
        ->assertViewIs('admin.event-schedules.index')
        ->assertSee('Test Seminar');
});

test('admin can view create form', function () {
    $this->withoutVite()->get(route('admin.event-schedules.create'))
        ->assertOk()
        ->assertViewIs('admin.event-schedules.form')
        ->assertViewHas('channels');
});

test('admin can create event schedule', function () {
    $this->post(route('admin.event-schedules.store'), [
        'title' => 'Konferensi Nasional',
        'location' => 'Jakarta',
        'subtitle' => 'Sesi Pembukaan',
        'time_display' => '08:00 - 10:00 WIB',
        'organizer' => 'Panitia',
        'starts_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addHours(3)->format('Y-m-d H:i:s'),
        'channel_ids' => [$this->channel->id],
    ])->assertRedirect(route('admin.event-schedules.index'));

    $schedule = EventSchedule::where('title', 'Konferensi Nasional')->first();
    expect($schedule)->not->toBeNull();
    expect($schedule->channels)->toHaveCount(1);
    expect($schedule->channels->first()->id)->toBe($this->channel->id);
});

test('title is required when creating schedule', function () {
    $this->post(route('admin.event-schedules.store'), [
        'title' => '',
        'starts_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addHours(3)->format('Y-m-d H:i:s'),
        'channel_ids' => [$this->channel->id],
    ])->assertSessionHasErrors('title');
});

test('ends_at must be after starts_at', function () {
    $this->post(route('admin.event-schedules.store'), [
        'title' => 'Test',
        'starts_at' => now()->addHours(3)->format('Y-m-d H:i:s'),
        'ends_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'channel_ids' => [$this->channel->id],
    ])->assertSessionHasErrors('ends_at');
});

test('at least one channel is required', function () {
    $this->post(route('admin.event-schedules.store'), [
        'title' => 'Test',
        'starts_at' => now()->addHour()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addHours(3)->format('Y-m-d H:i:s'),
        'channel_ids' => [],
    ])->assertSessionHasErrors('channel_ids');
});

test('admin can edit an event schedule', function () {
    $schedule = EventSchedule::factory()->upcomingIn(30)->create();
    $schedule->channels()->attach($this->channel);

    $this->withoutVite()->get(route('admin.event-schedules.edit', $schedule))
        ->assertOk()
        ->assertViewIs('admin.event-schedules.form')
        ->assertViewHas('eventSchedule', $schedule);
});

test('admin can update an event schedule', function () {
    $schedule = EventSchedule::factory()->upcomingIn(30)->create(['title' => 'Old Title']);
    $schedule->channels()->attach($this->channel);

    $this->put(route('admin.event-schedules.update', $schedule), [
        'title' => 'New Title',
        'starts_at' => $schedule->starts_at->format('Y-m-d H:i:s'),
        'ends_at' => $schedule->ends_at->format('Y-m-d H:i:s'),
        'channel_ids' => [$this->channel->id],
    ])->assertRedirect(route('admin.event-schedules.index'));

    expect($schedule->fresh()->title)->toBe('New Title');
});

test('admin can delete an event schedule', function () {
    $schedule = EventSchedule::factory()->upcomingIn(30)->create();
    $schedule->channels()->attach($this->channel);

    $this->delete(route('admin.event-schedules.destroy', $schedule))
        ->assertRedirect(route('admin.event-schedules.index'));

    expect(EventSchedule::find($schedule->id))->toBeNull();
});
