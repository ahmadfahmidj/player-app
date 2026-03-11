<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BroadcastState;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'channel_id' => 'required|exists:channels,id',
        ]);

        session(['active_channel_id' => $validated['channel_id']]);

        return back()->with('success', 'Channel switched.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'orientation' => 'required|integer|in:0,90,180,270',
        ]);

        $channel = Channel::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'is_main' => false,
            'orientation' => $validated['orientation'],
        ]);

        // initialize broadcast state for new channel
        BroadcastState::current($channel->id);

        session(['active_channel_id' => $channel->id]);

        return back()->with('success', 'Channel created successfully.');
    }

    public function destroy(Channel $channel)
    {
        if ($channel->is_main) {
            return back()->with('error', 'The main channel cannot be deleted.');
        }

        $channel->delete();

        // Switch back to main channel
        $mainChannel = Channel::where('is_main', true)->first();
        if ($mainChannel) {
            session(['active_channel_id' => $mainChannel->id]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Channel deleted successfully.');
    }
}
