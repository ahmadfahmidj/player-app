<?php

use App\Jobs\ProcessVideoOptimization;
use App\Jobs\ProcessVideoRotation;
use App\Models\Channel;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::factory()->create();
    $this->channel = Channel::create([
        'name' => 'Test Channel',
        'slug' => 'test-channel',
        'is_main' => true,
    ]);
});

test('video upload uses filename as title when title is blank', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->create('my-awesome-video.mp4', 1024, 'video/mp4');

    $response = $this->post(route('admin.videos.store'), [
        'title' => '',
        'video' => $file,
    ]);

    $response->assertRedirect(route('admin.videos'));

    $this->assertDatabaseHas('videos', [
        'title' => 'my-awesome-video',
    ]);
});

test('video upload uses provided title when given', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->create('some-file.mp4', 1024, 'video/mp4');

    $response = $this->post(route('admin.videos.store'), [
        'title' => 'Custom Title',
        'video' => $file,
    ]);

    $response->assertRedirect(route('admin.videos'));

    $this->assertDatabaseHas('videos', [
        'title' => 'Custom Title',
    ]);
});

test('video title can be updated via json request', function () {
    $this->actingAs($this->user);

    $video = Video::factory()->create(['title' => 'Old Title']);

    $response = $this->patchJson(route('admin.videos.update', $video), [
        'title' => 'New Title',
    ]);

    $response->assertSuccessful()
        ->assertJson(['success' => true, 'title' => 'New Title']);

    $this->assertDatabaseHas('videos', [
        'id' => $video->id,
        'title' => 'New Title',
    ]);
});

test('video title update requires a title', function () {
    $this->actingAs($this->user);

    $video = Video::factory()->create();

    $response = $this->patchJson(route('admin.videos.update', $video), [
        'title' => '',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

test('video update with rotate dispatches rotation job', function () {
    Queue::fake();

    $this->actingAs($this->user);

    $video = Video::factory()->create(['title' => 'My Video']);

    $response = $this->patchJson(route('admin.videos.update', $video), [
        'title' => 'My Video',
        'rotate' => true,
    ]);

    $response->assertSuccessful();

    Queue::assertPushed(ProcessVideoRotation::class, function ($job) use ($video) {
        return $job->video->id === $video->id;
    });
});

test('video update without rotate does not dispatch rotation job', function () {
    Queue::fake();

    $this->actingAs($this->user);

    $video = Video::factory()->create(['title' => 'My Video']);

    $response = $this->patchJson(route('admin.videos.update', $video), [
        'title' => 'Updated Title',
        'rotate' => false,
    ]);

    $response->assertSuccessful();

    Queue::assertNotPushed(ProcessVideoRotation::class);
});

test('video upload dispatches optimization job without rotate', function () {
    Queue::fake();

    $this->actingAs($this->user);

    $file = UploadedFile::fake()->create('test-video.mp4', 1024, 'video/mp4');

    $this->post(route('admin.videos.store'), [
        'title' => 'Test Video',
        'video' => $file,
    ]);

    Queue::assertPushed(ProcessVideoOptimization::class, function ($job) {
        return $job->rotate === false;
    });

    Queue::assertNotPushed(ProcessVideoRotation::class);
});

test('video upload with rotate dispatches optimization job with rotate true', function () {
    Queue::fake();

    $this->actingAs($this->user);

    $file = UploadedFile::fake()->create('portrait-video.mp4', 1024, 'video/mp4');

    $this->post(route('admin.videos.store'), [
        'title' => 'Portrait Video',
        'video' => $file,
        'rotate' => '1',
    ]);

    Queue::assertPushed(ProcessVideoOptimization::class, function ($job) {
        return $job->rotate === true;
    });
});

test('newly uploaded video has is_optimized false in database', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->create('new-video.mp4', 1024, 'video/mp4');

    $this->post(route('admin.videos.store'), [
        'title' => 'New Video',
        'video' => $file,
    ]);

    $this->assertDatabaseHas('videos', [
        'title' => 'New Video',
        'is_optimized' => false,
    ]);
});

test('video formatted duration displays correctly', function () {
    $short = Video::factory()->create(['duration' => 90]);
    expect($short->formatted_duration)->toBe('1:30');

    $long = Video::factory()->create(['duration' => 3661]);
    expect($long->formatted_duration)->toBe('1:01:01');

    $zero = Video::factory()->create(['duration' => 0]);
    expect($zero->formatted_duration)->toBe('0:00');
});
