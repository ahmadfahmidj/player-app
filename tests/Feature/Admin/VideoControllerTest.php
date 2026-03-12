<?php

use App\Models\User;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $this->channel = \App\Models\Channel::where('is_main', true)->first() ?? \App\Models\Channel::create([
        'name' => 'Main Channel',
        'slug' => 'main',
        'is_main' => true,
    ]);
    session(['active_channel_id' => $this->channel->id]);
});

test('guests are redirected from video store', function () {
    auth()->logout();

    $this->post(route('admin.videos.store'))->assertRedirect(route('login'));
});

test('authenticated user can upload a video', function () {
    $file = UploadedFile::fake()->create('test.mp4', 1024, 'video/mp4');

    $response = $this->post(route('admin.videos.store'), [
        'title' => 'Test Video',
        'video' => $file,
    ]);

    $response->assertRedirect(route('admin.videos'));

    $video = Video::first();
    Storage::disk('public')->assertExists('videos/'.$video->filename);

    $this->assertDatabaseHas('videos', [
        'title' => 'Test Video',
        'path' => 'videos/'.$video->filename,
    ]);

    $this->assertDatabaseHas('channel_video', [
        'channel_id' => $this->channel->id,
        'video_id' => $video->id,
    ]);
});

test('video is stored on the public disk not the local disk', function () {
    $file = UploadedFile::fake()->create('sample.mp4', 512, 'video/mp4');

    $this->post(route('admin.videos.store'), [
        'title' => 'Sample',
        'video' => $file,
    ]);

    $video = Video::first();
    Storage::disk('public')->assertExists('videos/'.$video->filename);
    Storage::disk('local')->assertMissing('public/videos/'.$video->filename);
});

test('authenticated user can delete a video from playlist', function () {
    $filename = 'existing.mp4';
    Storage::disk('public')->put('videos/'.$filename, 'fake content');

    $video = Video::factory()->create([
        'filename' => $filename,
        'path' => 'videos/'.$filename,
    ]);
    $this->channel->videos()->attach($video->id, ['order' => 1]);

    $response = $this->delete(route('admin.videos.destroy', $video));

    $response->assertRedirect(route('admin.videos'));

    // Test that the video is detached from the active channel
    $this->assertDatabaseMissing('channel_video', [
        'channel_id' => $this->channel->id,
        'video_id' => $video->id,
    ]);
});

test('authenticated user can add an existing video to playlist', function () {
    $video = Video::factory()->create();

    $response = $this->post(route('admin.videos.store'), [
        'existing_video_id' => $video->id,
    ]);

    $response->assertRedirect(route('admin.videos'));

    $this->assertDatabaseHas('channel_video', [
        'channel_id' => $this->channel->id,
        'video_id' => $video->id,
    ]);
});

test('uploading a new video with empty existing_video_id succeeds', function () {
    $file = UploadedFile::fake()->create('new.mp4', 1024, 'video/mp4');

    $response = $this->post(route('admin.videos.store'), [
        'existing_video_id' => '',
        'title' => 'New Video',
        'video' => $file,
    ]);

    $response->assertRedirect(route('admin.videos'));

    $video = Video::where('title', 'New Video')->first();
    $this->assertNotNull($video);
    $this->assertDatabaseHas('channel_video', [
        'channel_id' => $this->channel->id,
        'video_id' => $video->id,
    ]);
});

test('authenticated user can reorder videos in playlist', function () {
    $video1 = Video::factory()->create();
    $video2 = Video::factory()->create();

    $this->channel->videos()->attach($video1->id, ['order' => 1]);
    $this->channel->videos()->attach($video2->id, ['order' => 2]);

    $response = $this->patch(route('admin.videos.reorder'), [
        'order' => [$video2->id, $video1->id],
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('channel_video', [
        'channel_id' => $this->channel->id,
        'video_id' => $video2->id,
        'order' => 1,
    ]);

    $this->assertDatabaseHas('channel_video', [
        'channel_id' => $this->channel->id,
        'video_id' => $video1->id,
        'order' => 2,
    ]);
});

test('authenticated user can permanently delete a video from library', function () {
    $filename = 'deleteme.mp4';
    Storage::disk('public')->put('videos/'.$filename, 'fake content');

    $video = Video::factory()->create([
        'filename' => $filename,
        'path' => 'videos/'.$filename,
    ]);
    $this->channel->videos()->attach($video->id, ['order' => 1]);

    $response = $this->delete(route('admin.videos.force-destroy', $video));

    $response->assertRedirect(route('admin.videos'));

    $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    $this->assertDatabaseMissing('channel_video', ['video_id' => $video->id]);
    Storage::disk('public')->assertMissing('videos/'.$filename);
});
