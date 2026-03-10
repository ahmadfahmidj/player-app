<?php

use App\Http\Controllers\Api\PlayerStateController;
use App\Http\Controllers\Api\VideoEndedController;
use Illuminate\Support\Facades\Route;

Route::get('/player/state', PlayerStateController::class)->name('api.player.state');
Route::post('/player/video-ended', VideoEndedController::class)->name('api.player.video-ended');
