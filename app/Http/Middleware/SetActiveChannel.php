<?php

namespace App\Http\Middleware;

use App\Models\Channel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetActiveChannel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allChannels = Channel::all();
        \Illuminate\Support\Facades\View::share('allChannels', $allChannels);

        $activeChannelId = session('active_channel_id');
        $activeChannel = null;

        if ($activeChannelId) {
            $activeChannel = Channel::find($activeChannelId);
        }

        if (! $activeChannel) {
            $activeChannel = Channel::where('is_main', true)->first();
            if ($activeChannel) {
                session(['active_channel_id' => $activeChannel->id]);
            }
        }

        \Illuminate\Support\Facades\View::share('activeChannel', $activeChannel);

        $storagePath = storage_path('app');
        $totalBytes = disk_total_space($storagePath);
        $freeBytes = disk_free_space($storagePath);
        $usedBytes = $totalBytes - $freeBytes;
        $usedPercent = $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100) : 0;

        \Illuminate\Support\Facades\View::share('storageInfo', [
            'total' => $totalBytes,
            'free' => $freeBytes,
            'used' => $usedBytes,
            'percent' => $usedPercent,
        ]);

        return $next($request);
    }
}
