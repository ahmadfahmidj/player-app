<?php

namespace App\Http\Controllers;

use App\Models\ImageSlide;
use App\Models\Setting;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(): View
    {
        $channel = \App\Models\Channel::where('is_main', true)->firstOrFail();

        return $this->renderPlayer($channel);
    }

    public function show(string $slug): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $channel = \App\Models\Channel::where('slug', $slug)->first();
        if (! $channel) {
            return redirect()->route('player');
        }

        return $this->renderPlayer($channel);
    }

    private function renderPlayer(\App\Models\Channel $channel): View
    {
        $cid = $channel->id;
        $runningText = Setting::get($cid, 'running_text', 'Selamat datang di Rumah Sakit');
        $logoPath = Setting::get($cid, 'logo_path');
        $logoUrl = $logoPath ? asset('storage/'.$logoPath) : null;

        $overlay = [
            'show' => Setting::get($cid, 'overlay_show', '0') === '1',
            'location' => Setting::get($cid, 'overlay_location', ''),
            'subtitle' => Setting::get($cid, 'overlay_subtitle', ''),
            'title' => Setting::get($cid, 'overlay_title', ''),
            'time' => Setting::get($cid, 'overlay_time', ''),
            'organizer' => Setting::get($cid, 'overlay_organizer', ''),
        ];

        $screenOrientation = Setting::get($cid, 'screen_orientation', 'landscape');

        $imageSlides = ImageSlide::where('channel_id', $cid)
            ->orderBy('order')
            ->get()
            ->map(fn (ImageSlide $s) => ['url' => $s->url, 'duration' => $s->duration])
            ->values()
            ->all();

        return view('player', compact('runningText', 'logoUrl', 'overlay', 'channel', 'screenOrientation', 'imageSlides'));
    }
}
