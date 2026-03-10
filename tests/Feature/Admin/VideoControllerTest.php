<?php

use App\Models\User;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
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

test('authenticated user can delete a video', function () {
    $filename = 'existing.mp4';
    Storage::disk('public')->put('videos/'.$filename, 'fake content');

    $video = Video::factory()->create([
        'filename' => $filename,
        'path' => 'videos/'.$filename,
    ]);

    $response = $this->delete(route('admin.videos.destroy', $video));

    $response->assertRedirect(route('admin.videos'));
    $this->assertModelMissing($video);
    Storage::disk('public')->assertMissing('videos/'.$filename);
});
