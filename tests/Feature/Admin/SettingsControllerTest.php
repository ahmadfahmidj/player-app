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
    Setting::insert([
        ['key' => 'running_text', 'value' => 'Default text', 'created_at' => now(), 'updated_at' => now()],
        ['key' => 'logo_path', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
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

    expect(Setting::get('running_text'))->toBe('New ticker message');

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

    Storage::disk('public')->assertExists('logo/logo.png');
    expect(Setting::get('logo_path'))->toBe('logo/logo.png');

    Event::assertDispatched(LogoUpdated::class);
});

test('uploading new logo deletes old logo', function () {
    Storage::fake('public');
    Event::fake([LogoUpdated::class]);

    Storage::disk('public')->put('logo/logo.png', 'old content');
    Setting::set('logo_path', 'logo/logo.png');

    $file = UploadedFile::fake()->image('new-logo.jpg', 200, 200);

    $this->post(route('admin.settings.logo'), ['logo' => $file]);

    Storage::disk('public')->assertMissing('logo/logo.png');
    Storage::disk('public')->assertExists('logo/logo.jpg');
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
