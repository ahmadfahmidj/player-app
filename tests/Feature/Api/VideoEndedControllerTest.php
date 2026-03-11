<?php

use App\Events\VideoChanged;
use App\Models\BroadcastState;
use App\Models\Video;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->channel = \App\Models\Channel::where('is_main', true)->first() ?? \App\Models\Channel::create([
        'name' => 'Main Channel',
        'slug' => 'main',
        'is_main' => true,
    ]);
    BroadcastState::create([
        'channel_id' => $this->channel->id,
        'current_video_id' => null,
        'current_position' => 0,
        'is_playing' => true,
        'loop_mode' => 'none',
        'started_at' => now(),
        'updated_at' => now(),
    ]);
});

test('video ended endpoint is publicly accessible', function () {
    $this->postJson(route('api.player.video-ended', ['channel' => 'main']))->assertOk();
});

test('none mode stops playback', function () {
    $video = Video::factory()->create();
    $this->channel->videos()->attach($video->id, ['order' => 1]);
    $state = BroadcastState::current($this->channel->id);
    $state->update(['current_video_id' => $video->id]);

    $this->postJson(route('api.player.video-ended', ['channel' => 'main']))
        ->assertJson(['action' => 'stop']);

    $state->refresh();
    expect($state->is_playing)->toBeFalse()
        ->and($state->started_at)->toBeNull();
});

test('single mode repeats current video', function () {
    $video = Video::factory()->create();
    $this->channel->videos()->attach($video->id, ['order' => 1]);
    $state = BroadcastState::current($this->channel->id);
    $state->update(['current_video_id' => $video->id, 'loop_mode' => 'single']);

    $this->postJson(route('api.player.video-ended', ['channel' => 'main']))
        ->assertJson(['action' => 'repeat']);

    $state->refresh();
    expect($state->is_playing)->toBeTrue()
        ->and($state->current_position)->toBe(0.0);
});

test('playlist mode advances to next video', function () {
    Event::fake([VideoChanged::class]);

    $video1 = Video::factory()->create();
    $video2 = Video::factory()->create();
    $this->channel->videos()->attach($video1->id, ['order' => 1]);
    $this->channel->videos()->attach($video2->id, ['order' => 2]);

    $state = BroadcastState::current($this->channel->id);
    $state->update(['current_video_id' => $video1->id, 'loop_mode' => 'playlist']);

    $this->postJson(route('api.player.video-ended', ['channel' => 'main']))
        ->assertJson(['action' => 'next', 'video_id' => $video2->id]);

    $state->refresh();
    expect($state->current_video_id)->toBe($video2->id)
        ->and($state->is_playing)->toBeTrue()
        ->and($state->current_position)->toBe(0.0);

    Event::assertDispatched(VideoChanged::class, function ($event) use ($video2) {
        return $event->video_id === $video2->id;
    });
});

test('playlist mode wraps around to first video after last', function () {
    Event::fake([VideoChanged::class]);

    $video1 = Video::factory()->create();
    $video2 = Video::factory()->create();
    $this->channel->videos()->attach($video1->id, ['order' => 1]);
    $this->channel->videos()->attach($video2->id, ['order' => 2]);

    $state = BroadcastState::current($this->channel->id);
    $state->update(['current_video_id' => $video2->id, 'loop_mode' => 'playlist']);

    $this->postJson(route('api.player.video-ended', ['channel' => 'main']))
        ->assertJson(['action' => 'next', 'video_id' => $video1->id]);

    $state->refresh();
    expect($state->current_video_id)->toBe($video1->id);
});

test('playlist mode with single video replays it', function () {
    Event::fake([VideoChanged::class]);

    $video = Video::factory()->create();
    $this->channel->videos()->attach($video->id, ['order' => 1]);

    $state = BroadcastState::current($this->channel->id);
    $state->update(['current_video_id' => $video->id, 'loop_mode' => 'playlist']);

    $this->postJson(route('api.player.video-ended', ['channel' => 'main']))
        ->assertJson(['action' => 'next', 'video_id' => $video->id]);
});

test('playlist mode with no videos stops playback', function () {
    $state = BroadcastState::current($this->channel->id);
    $state->update(['loop_mode' => 'playlist']);

    $this->postJson(route('api.player.video-ended', ['channel' => 'main']))
        ->assertJson(['action' => 'stop']);

    $state->refresh();
    expect($state->is_playing)->toBeFalse();
});
