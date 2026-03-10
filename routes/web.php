<?php

use App\Http\Controllers\Admin\PlaybackController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

// Public player page (primary route)
Route::get('/', [PlayerController::class, 'index'])->name('player');
Route::get('/player', fn () => redirect()->route('player'));
Route::get('/jadwal', [PlayerController::class, 'jadwal'])->name('player.jadwal');

// Admin routes (auth required)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [PlaybackController::class, 'index'])->name('dashboard');

    // Video management
    Route::get('/videos', [VideoController::class, 'index'])->name('videos');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::patch('/videos/reorder', [VideoController::class, 'reorder'])->name('videos.reorder');

    // Playback control
    Route::post('/playback/play', [PlaybackController::class, 'play'])->name('playback.play');
    Route::post('/playback/pause', [PlaybackController::class, 'pause'])->name('playback.pause');
    Route::post('/playback/seek', [PlaybackController::class, 'seek'])->name('playback.seek');
    Route::post('/playback/change', [PlaybackController::class, 'change'])->name('playback.change');
    Route::post('/playback/loop', [PlaybackController::class, 'loop'])->name('playback.loop');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/running-text', [SettingsController::class, 'updateRunningText'])->name('settings.running-text');
    Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo');
});
