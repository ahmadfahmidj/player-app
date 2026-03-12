<?php

use App\Http\Controllers\Admin\EventScheduleController;
use App\Http\Controllers\Admin\ImageSlideController;
use App\Http\Controllers\Admin\PlaybackController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

// Public player page (primary route)
Route::get('/', [PlayerController::class, 'index'])->name('player');
Route::get('/player', fn () => redirect()->route('player'));

// Admin routes (auth required)
Route::middleware(['auth', \App\Http\Middleware\SetActiveChannel::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [PlaybackController::class, 'index'])->name('dashboard');

    // Channels
    Route::post('/channels/switch', [\App\Http\Controllers\Admin\ChannelController::class, 'switch'])->name('channels.switch');
    Route::post('/channels', [\App\Http\Controllers\Admin\ChannelController::class, 'store'])->name('channels.store');
    Route::put('/channels/{channel}', [\App\Http\Controllers\Admin\ChannelController::class, 'update'])->name('channels.update');
    Route::delete('/channels/{channel}', [\App\Http\Controllers\Admin\ChannelController::class, 'destroy'])->name('channels.destroy');
    Route::post('/channels/refresh', [\App\Http\Controllers\Admin\ChannelController::class, 'refresh'])->name('channels.refresh');

    // Video management
    Route::get('/videos', [VideoController::class, 'index'])->name('videos');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::patch('/videos/reorder', [VideoController::class, 'reorder'])->name('videos.reorder');
    Route::patch('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::delete('/videos/{video}/permanent', [VideoController::class, 'forceDestroy'])->name('videos.force-destroy');

    // Playback control
    Route::post('/playback/play', [PlaybackController::class, 'play'])->name('playback.play');
    Route::post('/playback/pause', [PlaybackController::class, 'pause'])->name('playback.pause');
    Route::post('/playback/seek', [PlaybackController::class, 'seek'])->name('playback.seek');
    Route::post('/playback/change', [PlaybackController::class, 'change'])->name('playback.change');
    Route::post('/playback/loop', [PlaybackController::class, 'loop'])->name('playback.loop');

    // Event Schedules
    Route::resource('/event-schedules', EventScheduleController::class)
        ->except(['show'])
        ->names('event-schedules');

    // Image Slides
    Route::get('/image-slides', [ImageSlideController::class, 'index'])->name('image-slides');
    Route::post('/image-slides', [ImageSlideController::class, 'store'])->name('image-slides.store');
    Route::patch('/image-slides/reorder', [ImageSlideController::class, 'reorder'])->name('image-slides.reorder');
    Route::patch('/image-slides/{imageSlide}', [ImageSlideController::class, 'update'])->name('image-slides.update');
    Route::delete('/image-slides/{imageSlide}', [ImageSlideController::class, 'destroy'])->name('image-slides.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/running-text', [SettingsController::class, 'updateRunningText'])->name('settings.running-text');
    Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo');
    Route::delete('/settings/logo', [SettingsController::class, 'destroyLogo'])->name('settings.logo.destroy');
    Route::post('/settings/event-overlay', [SettingsController::class, 'updateEventOverlay'])->name('settings.event-overlay');
    Route::post('/settings/screen-orientation', [SettingsController::class, 'updateScreenOrientation'])->name('settings.screen-orientation');
});

// Public sub-channels
Route::get('/{slug}', [PlayerController::class, 'show'])->name('player.channel');
