<?php

use App\Events\ImageSlidesUpdated;
use App\Jobs\ProcessImageRotation;
use App\Models\Channel;
use App\Models\ImageSlide;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
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
    session(['active_channel_id' => $this->channel->id]);
});

test('uploading a slide creates image slide record attached to channel', function () {
    Event::fake();
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('banner.jpg', 1920, 1080);

    $response = $this->post(route('admin.image-slides.store'), [
        'title' => 'My Banner',
        'image' => $file,
        'duration' => 8,
    ]);

    $response->assertRedirect(route('admin.image-slides'));

    $this->assertDatabaseHas('image_slides', [
        'channel_id' => $this->channel->id,
        'title' => 'My Banner',
        'duration' => 8,
    ]);
});

test('upload uses filename as title when title is blank', function () {
    Event::fake();
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('my-photo.jpg', 800, 600);

    $this->post(route('admin.image-slides.store'), [
        'title' => '',
        'image' => $file,
        'duration' => 5,
    ]);

    $this->assertDatabaseHas('image_slides', [
        'channel_id' => $this->channel->id,
        'title' => 'my-photo',
    ]);
});

test('upload with rotate dispatches ProcessImageRotation job', function () {
    Queue::fake();
    Event::fake();
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('photo.png', 400, 800);

    $this->post(route('admin.image-slides.store'), [
        'image' => $file,
        'duration' => 5,
        'rotate' => '1',
    ]);

    Queue::assertPushed(ProcessImageRotation::class);
});

test('upload without rotate does not dispatch rotation job', function () {
    Queue::fake();
    Event::fake();
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

    $this->post(route('admin.image-slides.store'), [
        'image' => $file,
        'duration' => 5,
    ]);

    Queue::assertNotPushed(ProcessImageRotation::class);
});

test('upload broadcasts ImageSlidesUpdated event', function () {
    Event::fake();
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('slide.jpg');

    $this->post(route('admin.image-slides.store'), [
        'image' => $file,
        'duration' => 5,
    ]);

    Event::assertDispatched(ImageSlidesUpdated::class, function ($event) {
        return $event->channelSlug === $this->channel->slug;
    });
});

test('duration must be between 1 and 300', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('test.jpg');

    $this->post(route('admin.image-slides.store'), [
        'image' => $file,
        'duration' => 0,
    ])->assertSessionHasErrors('duration');

    $this->post(route('admin.image-slides.store'), [
        'image' => $file,
        'duration' => 301,
    ])->assertSessionHasErrors('duration');
});

test('reorder updates order column for each slide', function () {
    Event::fake();
    $this->actingAs($this->user);

    $slide1 = ImageSlide::create([
        'channel_id' => $this->channel->id,
        'title' => 'First',
        'filename' => 'a.jpg',
        'path' => 'slides/a.jpg',
        'duration' => 5,
        'order' => 1,
    ]);
    $slide2 = ImageSlide::create([
        'channel_id' => $this->channel->id,
        'title' => 'Second',
        'filename' => 'b.jpg',
        'path' => 'slides/b.jpg',
        'duration' => 5,
        'order' => 2,
    ]);

    // Swap order
    $response = $this->patchJson(route('admin.image-slides.reorder'), [
        'order' => [$slide2->id, $slide1->id],
    ]);

    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('image_slides', ['id' => $slide2->id, 'order' => 1]);
    $this->assertDatabaseHas('image_slides', ['id' => $slide1->id, 'order' => 2]);
});

test('deleting a slide removes file and database record', function () {
    Event::fake();
    $this->actingAs($this->user);

    Storage::disk('public')->put('slides/test.jpg', 'fake-content');

    $slide = ImageSlide::create([
        'channel_id' => $this->channel->id,
        'title' => 'To Delete',
        'filename' => 'test.jpg',
        'path' => 'slides/test.jpg',
        'duration' => 5,
        'order' => 1,
    ]);

    $response = $this->delete(route('admin.image-slides.destroy', $slide));

    $response->assertRedirect(route('admin.image-slides'));

    $this->assertDatabaseMissing('image_slides', ['id' => $slide->id]);
    Storage::disk('public')->assertMissing('slides/test.jpg');
});

test('updating a slide changes title and duration', function () {
    Event::fake();
    $this->actingAs($this->user);

    $slide = ImageSlide::create([
        'channel_id' => $this->channel->id,
        'title' => 'Original',
        'filename' => 'img.jpg',
        'path' => 'slides/img.jpg',
        'duration' => 5,
        'order' => 1,
    ]);

    $response = $this->patchJson(route('admin.image-slides.update', $slide), [
        'title' => 'Updated',
        'duration' => 15,
    ]);

    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('image_slides', [
        'id' => $slide->id,
        'title' => 'Updated',
        'duration' => 15,
    ]);
});

test('update with rotate dispatches rotation job', function () {
    Queue::fake();
    Event::fake();
    $this->actingAs($this->user);

    $slide = ImageSlide::create([
        'channel_id' => $this->channel->id,
        'title' => 'Slide',
        'filename' => 'img.jpg',
        'path' => 'slides/img.jpg',
        'duration' => 5,
        'order' => 1,
    ]);

    $this->patchJson(route('admin.image-slides.update', $slide), [
        'duration' => 5,
        'rotate' => true,
    ]);

    Queue::assertPushed(ProcessImageRotation::class, function ($job) use ($slide) {
        return $job->imageSlide->id === $slide->id;
    });
});
