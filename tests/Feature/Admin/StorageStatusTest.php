<?php

use App\Models\Channel;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->channel = Channel::where('is_main', true)->first() ?? Channel::create([
        'name' => 'Main Channel',
        'slug' => 'main',
        'is_main' => true,
    ]);
    session(['active_channel_id' => $this->channel->id]);
});

test('admin pages display storage status indicator', function () {
    $response = $this->get(route('admin.videos'));

    $response->assertOk();
    $response->assertSee(__('free'));
});
