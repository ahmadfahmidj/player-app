<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerStateResource;
use App\Models\BroadcastState;

class PlayerStateController extends Controller
{
    public function __invoke(\Illuminate\Http\Request $request): PlayerStateResource
    {
        $slug = $request->query('channel', 'main');
        $channel = \App\Models\Channel::where('slug', $slug)->first() ?? \App\Models\Channel::where('is_main', true)->first();
        if (! $channel) {
            abort(404);
        }

        return new PlayerStateResource(BroadcastState::current($channel->id));
    }
}
