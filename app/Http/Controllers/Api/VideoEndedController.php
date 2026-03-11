<?php

namespace App\Http\Controllers\Api;

use App\Events\VideoChanged;
use App\Http\Controllers\Controller;
use App\Models\BroadcastState;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

class VideoEndedController extends Controller
{
    public function __invoke(\Illuminate\Http\Request $request): JsonResponse
    {
        $slug = $request->query('channel', 'main');
        $channel = \App\Models\Channel::where('slug', $slug)->first() ?? \App\Models\Channel::where('is_main', true)->first();
        if (! $channel) {
            abort(404);
        }

        $state = BroadcastState::current($channel->id);

        if ($state->loop_mode === 'single') {
            $state->update([
                'current_position' => 0,
                'is_playing' => true,
                'started_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['action' => 'repeat']);
        }

        if ($state->loop_mode === 'playlist') {
            $nextVideo = $this->getNextVideo($channel, $state->current_video_id);

            if ($nextVideo) {
                $state->update([
                    'current_video_id' => $nextVideo->id,
                    'current_position' => 0,
                    'is_playing' => true,
                    'started_at' => now(),
                    'updated_at' => now(),
                ]);

                VideoChanged::dispatch($nextVideo->id, 0.0, $state->loop_mode, $channel->slug);

                return response()->json(['action' => 'next', 'video_id' => $nextVideo->id]);
            }
        }

        // loop_mode === 'none' or no next video
        $state->update([
            'is_playing' => false,
            'current_position' => 0,
            'started_at' => null,
            'updated_at' => now(),
        ]);

        return response()->json(['action' => 'stop']);
    }

    private function getNextVideo(\App\Models\Channel $channel, ?int $currentVideoId): ?Video
    {
        $videos = $channel->videos()->orderByPivot('order')->get();

        if ($videos->isEmpty()) {
            return null;
        }

        if (! $currentVideoId) {
            return $videos->first();
        }

        $currentIndex = $videos->search(fn (Video $v) => $v->id === $currentVideoId);

        if ($currentIndex === false) {
            return $videos->first();
        }

        $nextIndex = $currentIndex + 1;

        // Wrap around to first video in playlist mode
        if ($nextIndex >= $videos->count()) {
            return $videos->first();
        }

        return $videos->get($nextIndex);
    }
}
