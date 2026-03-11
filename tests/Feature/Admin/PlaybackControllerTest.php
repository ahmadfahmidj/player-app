<?php

use App\Events\LoopModeChanged;
use App\Events\VideoChanged;
use App\Events\VideoPaused;
use App\Events\VideoPlayed;
use App\Events\VideoSeeked;
use App\Models\BroadcastState;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    /** @var \Tests\TestCase $this */
    $this->actingAs(User::factory()->create());
    $this->channel = \App\Models\Channel::where('is_main', true)->first() ?? \App\Models\Channel::create([
        'name' => 'Main Channel',
        'slug' => 'main',
        'is_main' => true,
    ]);
    session(['active_channel_id' => $this->channel->id]);

    BroadcastState::create([
        'channel_id' => $this->channel->id,
        'current_video_id' => null,
        'current_position' => 0,
        'is_playing' => false,
        'loop_mode' => 'none',
        'started_at' => null,
        'updated_at' => now(),
    ]);
});

test('guests cannot access playback controls', function () {
    /** @var \Tests\TestCase $this */
    auth()->logout();

    $this->post(route('admin.playback.play'))->assertRedirect(route('login'));
    $this->post(route('admin.playback.pause'))->assertRedirect(route('login'));
    $this->post(route('admin.playback.seek'))->assertRedirect(route('login'));
    $this->post(route('admin.playback.change'))->assertRedirect(route('login'));
    $this->post(route('admin.playback.loop'))->assertRedirect(route('login'));
});

test('admin can view dashboard', function () {
    /** @var \Tests\TestCase $this */
    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewIs('admin.dashboard')
        ->assertViewHas(['videos', 'state']);
});

test('play updates state and dispatches event', function () {
    /** @var \Tests\TestCase $this */
    Event::fake([VideoPlayed::class]);

    $video = Video::factory()->create();
    $state = BroadcastState::current($this->channel->id);
    $state->update(['current_video_id' => $video->id]);

    $this->post(route('admin.playback.play'))
        ->assertJson(['success' => true]);

    $state->refresh();
    expect($state->is_playing)->toBeTrue()
        ->and($state->started_at)->not->toBeNull();

    Event::assertDispatched(VideoPlayed::class);
});

test('pause updates state and dispatches event', function () {
    /** @var \Tests\TestCase $this */
    Event::fake([VideoPaused::class]);

    $state = BroadcastState::current($this->channel->id);
    $state->update([
        'is_playing' => true,
        'current_position' => 30.5,
        'started_at' => now(),
    ]);

    $this->post(route('admin.playback.pause'))
        ->assertJson(['success' => true]);

    $state->refresh();
    expect($state->is_playing)->toBeFalse()
        ->and($state->started_at)->toBeNull();

    Event::assertDispatched(VideoPaused::class);
});

test('seek updates position and dispatches event', function () {
    /** @var \Tests\TestCase $this */
    Event::fake([VideoSeeked::class]);

    $this->post(route('admin.playback.seek'), ['position' => 45.5])
        ->assertJson(['success' => true]);

    $state = BroadcastState::current($this->channel->id);
    expect($state->current_position)->toBe(45.5);

    Event::assertDispatched(VideoSeeked::class, function ($event) {
        return $event->position === 45.5;
    });
});

test('seek validates position is numeric and non-negative', function () {
    /** @var \Tests\TestCase $this */
    $this->post(route('admin.playback.seek'), ['position' => -5])
        ->assertSessionHasErrors('position');

    $this->post(route('admin.playback.seek'), ['position' => 'abc'])
        ->assertSessionHasErrors('position');
});

test('change switches video and dispatches event', function () {
    /** @var \Tests\TestCase $this */
    Event::fake([VideoChanged::class]);

    $video = Video::factory()->create();

    $this->post(route('admin.playback.change'), ['video_id' => $video->id])
        ->assertJson(['success' => true]);

    $state = BroadcastState::current($this->channel->id);
    expect($state->current_video_id)->toBe($video->id)
        ->and($state->current_position)->toBe(0.0)
        ->and($state->is_playing)->toBeTrue();

    Event::assertDispatched(VideoChanged::class, function ($event) use ($video) {
        return $event->video_id === $video->id && $event->position === 0.0;
    });
});

test('change validates video exists', function () {
    /** @var \Tests\TestCase $this */
    $this->post(route('admin.playback.change'), ['video_id' => 999])
        ->assertSessionHasErrors('video_id');
});

test('loop updates mode and dispatches event', function () {
    /** @var \Tests\TestCase $this */
    Event::fake([LoopModeChanged::class]);

    $this->post(route('admin.playback.loop'), ['loop_mode' => 'playlist'])
        ->assertJson(['success' => true]);

    $state = BroadcastState::current($this->channel->id);
    expect($state->loop_mode)->toBe('playlist');

    Event::assertDispatched(LoopModeChanged::class, function ($event) {
        return $event->loop_mode === 'playlist';
    });
});

test('loop validates mode is valid', function () {
    /** @var \Tests\TestCase $this */
    $this->post(route('admin.playback.loop'), ['loop_mode' => 'invalid'])
        ->assertSessionHasErrors('loop_mode');
});

test('loop cycles through all valid modes', function () {
    /** @var \Tests\TestCase $this */
    Event::fake([LoopModeChanged::class]);

    foreach (['none', 'single', 'playlist'] as $mode) {
        $this->post(route('admin.playback.loop'), ['loop_mode' => $mode])
            ->assertJson(['success' => true]);

        expect(BroadcastState::current($this->channel->id)->loop_mode)->toBe($mode);
    }
});
