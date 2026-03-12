<?php

namespace App\Http\Controllers\Admin;

use App\Events\ForceRefresh;
use App\Http\Controllers\Controller;
use App\Models\BroadcastState;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
        ]);

        session(['active_channel_id' => $validated['channel_id']]);

        return back()->with('success', __('Channel switched.'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:channels,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'orientation' => 'required|integer|in:0,90,180,270',
        ]);

        $slug = ! empty($validated['slug']) ? $validated['slug'] : Str::slug($validated['name']);

        $channel = Channel::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'is_main' => false,
            'orientation' => $validated['orientation'],
        ]);

        // initialize broadcast state for new channel
        BroadcastState::current($channel->id);

        session(['active_channel_id' => $channel->id]);

        return back()->with('success', __('Channel created successfully.'));
    }

    public function update(Request $request, Channel $channel)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:channels,slug,'.$channel->id.'|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
        ]);

        $channel->update([
            'slug' => $validated['slug'],
        ]);

        return back()->with('success', __('Channel slug updated successfully.'));
    }

    public function refresh(): \Illuminate\Http\JsonResponse
    {
        $channel = View::shared('activeChannel');

        ForceRefresh::dispatch($channel->slug);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Channel $channel)
    {
        if ($channel->is_main) {
            return back()->with('error', __('The main channel cannot be deleted.'));
        }

        $channel->delete();

        // Switch back to main channel
        $mainChannel = Channel::where('is_main', true)->first();
        if ($mainChannel) {
            session(['active_channel_id' => $mainChannel->id]);
        }

        return redirect()->route('admin.dashboard')->with('success', __('Channel deleted successfully.'));
    }
}
