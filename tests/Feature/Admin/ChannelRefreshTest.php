<?php

use App\Events\ForceRefresh;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    /** @var \Tests\TestCase $this */
    $this->actingAs(User::factory()->create());
    $this->channel = Channel::where('is_main', true)->first() ?? Channel::create([
        'name' => 'Main Channel',
        'slug' => 'main',
        'is_main' => true,
    ]);
    session(['active_channel_id' => $this->channel->id]);
});

it('dispatches ForceRefresh event for the active channel', function () {
    Event::fake([ForceRefresh::class]);

    $this->postJson(route('admin.channels.refresh'))
        ->assertOk()
        ->assertJson(['status' => 'ok']);

    Event::assertDispatched(ForceRefresh::class, function ($event) {
        return $event->channelSlug === $this->channel->slug;
    });
});

it('requires authentication', function () {
    auth()->logout();

    $this->postJson(route('admin.channels.refresh'))
        ->assertUnauthorized();
});
