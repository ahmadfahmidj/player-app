<?php

use App\Models\BroadcastState;
use App\Models\Setting;
use App\Models\Video;

beforeEach(function () {
    Setting::insert([
        ['key' => 'running_text', 'value' => 'Welcome', 'created_at' => now(), 'updated_at' => now()],
        ['key' => 'logo_path', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
    ]);
    BroadcastState::create([
        'current_video_id' => null,
        'current_position' => 0,
        'is_playing' => false,
        'loop_mode' => 'none',
        'started_at' => null,
        'updated_at' => now(),
    ]);
});

test('player state endpoint is publicly accessible', function () {
    $this->getJson(route('api.player.state'))->assertOk();
});

test('player state returns correct structure', function () {
    $this->getJson(route('api.player.state'))
        ->assertOk()
        ->assertJsonStructure([
            'current_video_id',
            'video_url',
            'video_title',
            'current_position',
            'is_playing',
            'loop_mode',
            'running_text',
            'logo_url',
        ]);
});

test('player state returns video details when video is set', function () {
    $video = Video::factory()->create(['title' => 'Test Video', 'filename' => 'test.mp4']);

    BroadcastState::current()->update(['current_video_id' => $video->id, 'current_position' => 15.5, 'loop_mode' => 'single']);

    $this->getJson(route('api.player.state'))
        ->assertOk()
        ->assertJson([
            'current_video_id' => $video->id,
            'video_title' => 'Test Video',
            'current_position' => 15.5,
            'is_playing' => false,
            'loop_mode' => 'single',
            'running_text' => 'Welcome',
            'logo_url' => null,
        ]);
});

test('player state returns null video fields when no video is set', function () {
    $this->getJson(route('api.player.state'))
        ->assertOk()
        ->assertJson([
            'current_video_id' => null,
            'video_url' => null,
            'video_title' => null,
        ]);
});

test('player state includes running text from settings', function () {
    Setting::set('running_text', 'Custom ticker');

    $this->getJson(route('api.player.state'))
        ->assertOk()
        ->assertJson(['running_text' => 'Custom ticker']);
});

test('player state includes logo url when logo is set', function () {
    Setting::set('logo_path', 'logo/hospital.png');

    $response = $this->getJson(route('api.player.state'))->assertOk();

    expect($response->json('logo_url'))->toContain('logo/hospital.png');
});
