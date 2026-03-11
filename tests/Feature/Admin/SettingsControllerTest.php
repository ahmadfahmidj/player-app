<?php

use App\Events\LogoUpdated;
use App\Events\RunningTextUpdated;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->channel = \App\Models\Channel::where('is_main', true)->first() ?? \App\Models\Channel::create([
        'name' => 'Main Channel',
        'slug' => 'main',
        'is_main' => true,
    ]);
    session(['active_channel_id' => $this->channel->id]);
    Setting::insert([
        ['channel_id' => $this->channel->id, 'key' => 'running_text', 'value' => 'Default text', 'created_at' => now(), 'updated_at' => now()],
        ['channel_id' => $this->channel->id, 'key' => 'logo_path', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

test('guests cannot access settings', function () {
    auth()->logout();

    $this->get(route('admin.settings'))->assertRedirect(route('login'));
    $this->post(route('admin.settings.running-text'))->assertRedirect(route('login'));
    $this->post(route('admin.settings.logo'))->assertRedirect(route('login'));
});

test('admin can view settings page', function () {
    $this->get(route('admin.settings'))
        ->assertOk()
        ->assertViewIs('admin.settings')
        ->assertViewHas('runningText', 'Default text');
});

test('admin can update running text', function () {
    Event::fake([RunningTextUpdated::class]);

    $this->post(route('admin.settings.running-text'), ['text' => 'New ticker message'])
        ->assertRedirect(route('admin.settings'));

    expect(Setting::get($this->channel->id, 'running_text'))->toBe('New ticker message');

    Event::assertDispatched(RunningTextUpdated::class, function ($event) {
        return $event->text === 'New ticker message';
    });
});

test('running text is required', function () {
    $this->post(route('admin.settings.running-text'), ['text' => ''])
        ->assertSessionHasErrors('text');
});

test('running text cannot exceed 1000 characters', function () {
    $this->post(route('admin.settings.running-text'), ['text' => str_repeat('a', 1001)])
        ->assertSessionHasErrors('text');
});

test('admin can upload a logo', function () {
    Storage::fake('public');
    Event::fake([LogoUpdated::class]);

    $file = UploadedFile::fake()->image('hospital-logo.png', 200, 200);

    $this->post(route('admin.settings.logo'), ['logo' => $file])
        ->assertRedirect(route('admin.settings'));

    $newPath = Setting::get($this->channel->id, 'logo_path');
    Storage::disk('public')->assertExists($newPath);

    Event::assertDispatched(LogoUpdated::class);
});

test('uploading new logo deletes old logo', function () {
    Storage::fake('public');
    Event::fake([LogoUpdated::class]);

    Storage::disk('public')->put('logo/logo.png', 'old content');
    Setting::set($this->channel->id, 'logo_path', 'logo/logo.png');

    $file = UploadedFile::fake()->image('new-logo.jpg', 200, 200);

    $this->post(route('admin.settings.logo'), ['logo' => $file]);

    Storage::disk('public')->assertMissing('logo/logo.png');
});

test('logo upload validates file type', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->post(route('admin.settings.logo'), ['logo' => $file])
        ->assertSessionHasErrors('logo');
});

test('logo upload validates file size', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('huge-logo.png')->size(3000);

    $this->post(route('admin.settings.logo'), ['logo' => $file])
        ->assertSessionHasErrors('logo');
});

test('admin can update screen orientation to landscape', function () {
    $this->post(route('admin.settings.screen-orientation'), ['screen_orientation' => 'landscape'])
        ->assertRedirect(route('admin.settings'));

    expect(Setting::get($this->channel->id, 'screen_orientation'))->toBe('landscape');
});

test('admin can update screen orientation to portrait 90', function () {
    $this->post(route('admin.settings.screen-orientation'), ['screen_orientation' => 'portrait'])
        ->assertRedirect(route('admin.settings'));

    expect(Setting::get($this->channel->id, 'screen_orientation'))->toBe('portrait');
});

test('admin can update screen orientation to portrait 180', function () {
    $this->post(route('admin.settings.screen-orientation'), ['screen_orientation' => 'portrait_180'])
        ->assertRedirect(route('admin.settings'));

    expect(Setting::get($this->channel->id, 'screen_orientation'))->toBe('portrait_180');
});

test('admin can update screen orientation to portrait 270', function () {
    $this->post(route('admin.settings.screen-orientation'), ['screen_orientation' => 'portrait_270'])
        ->assertRedirect(route('admin.settings'));

    expect(Setting::get($this->channel->id, 'screen_orientation'))->toBe('portrait_270');
});

test('screen orientation rejects invalid values', function () {
    $this->post(route('admin.settings.screen-orientation'), ['screen_orientation' => 'invalid'])
        ->assertSessionHasErrors('screen_orientation');
});

test('settings page shows saved screen orientation', function () {
    Setting::set($this->channel->id, 'screen_orientation', 'portrait_270');

    $this->get(route('admin.settings'))
        ->assertOk()
        ->assertViewHas('screenOrientation', 'portrait_270');
});
