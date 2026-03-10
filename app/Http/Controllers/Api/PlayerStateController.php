<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerStateResource;
use App\Models\BroadcastState;

class PlayerStateController extends Controller
{
    public function __invoke(): PlayerStateResource
    {
        return new PlayerStateResource(BroadcastState::current());
    }
}
