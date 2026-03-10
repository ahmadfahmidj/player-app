<?php

namespace App\Http\Controllers\Admin;

use App\Events\LoopModeChanged;
use App\Events\VideoChanged;
use App\Events\VideoPaused;
use App\Events\VideoPlayed;
use App\Events\VideoSeeked;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlaybackRequest;
use App\Models\BroadcastState;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PlaybackController extends Controller
{
    public function index(): View
    {
        $videos = Video::query()->orderBy('order')->get();
        $state = BroadcastState::current();

        return view('admin.dashboard', compact('videos', 'state'));
    }

    public function play(PlaybackRequest $request): JsonResponse
    {
        $state = BroadcastState::current();
        $state->update([
            'is_playing' => true,
            'started_at' => now(),
            'updated_at' => now(),
        ]);

        VideoPlayed::dispatch(
            (int) $state->current_video_id,
            (float) $state->current_position,
            microtime(true),
        );

        return response()->json(['success' => true]);
    }

    public function pause(PlaybackRequest $request): JsonResponse
    {
        $state = BroadcastState::current();
        $position = $state->calculateCurrentPosition();

        $state->update([
            'is_playing' => false,
            'current_position' => $position,
            'started_at' => null,
            'updated_at' => now(),
        ]);

        VideoPaused::dispatch($position, microtime(true));

        return response()->json(['success' => true]);
    }

    public function seek(PlaybackRequest $request): JsonResponse
    {
        $position = (float) $request->input('position', 0);
        $state = BroadcastState::current();

        $state->update([
            'current_position' => $position,
            'started_at' => $state->is_playing ? now() : $state->started_at,
            'updated_at' => now(),
        ]);

        VideoSeeked::dispatch($position, microtime(true));

        return response()->json(['success' => true]);
    }

    public function change(PlaybackRequest $request): JsonResponse
    {
        $videoId = (int) $request->input('video_id');
        $video = Video::findOrFail($videoId);
        $state = BroadcastState::current();

        $state->update([
            'current_video_id' => $video->id,
            'current_position' => 0,
            'is_playing' => true,
            'started_at' => now(),
            'updated_at' => now(),
        ]);

        VideoChanged::dispatch($video->id, 0.0, $state->loop_mode);

        return response()->json(['success' => true]);
    }

    public function loop(PlaybackRequest $request): JsonResponse
    {
        $loopMode = $request->input('loop_mode', 'none');
        $state = BroadcastState::current();

        $state->update([
            'loop_mode' => $loopMode,
            'updated_at' => now(),
        ]);

        LoopModeChanged::dispatch($loopMode);

        return response()->json(['success' => true]);
    }
}
