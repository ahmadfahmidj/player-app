<?php

namespace App\Http\Controllers\Admin;

use App\Events\EventOverlayUpdated;
use App\Events\LogoUpdated;
use App\Events\RunningTextUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventOverlayRequest;
use App\Http\Requests\Admin\RunningTextRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $cid = $activeChannel->id;

        $runningText = Setting::get($cid, 'running_text', '');
        $logoPath = Setting::get($cid, 'logo_path');
        $logoUrl = $logoPath ? asset('storage/'.$logoPath) : null;

        $overlayShow = Setting::get($cid, 'overlay_show', '0');
        $overlayLocation = Setting::get($cid, 'overlay_location', '');
        $overlaySubtitle = Setting::get($cid, 'overlay_subtitle', '');
        $overlayTitle = Setting::get($cid, 'overlay_title', '');
        $overlayTime = Setting::get($cid, 'overlay_time', '');
        $overlayOrganizer = Setting::get($cid, 'overlay_organizer', '');
        $screenOrientation = Setting::get($cid, 'screen_orientation', 'landscape');

        return view('admin.settings', compact(
            'runningText',
            'logoUrl',
            'overlayShow',
            'overlayLocation',
            'overlaySubtitle',
            'overlayTitle',
            'overlayTime',
            'overlayOrganizer',
            'screenOrientation'
        ));
    }

    public function updateRunningText(RunningTextRequest $request): RedirectResponse
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $cid = $activeChannel->id;

        $text = $request->string('text')->toString();
        Setting::set($cid, 'running_text', $text);

        // We will make events channel specific later, for now we will send the channel slug
        RunningTextUpdated::dispatch($text, $activeChannel->slug);

        return redirect()->route('admin.settings')->with('success', 'Running text updated.');
    }

    public function updateScreenOrientation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'screen_orientation' => 'required|in:landscape,portrait',
        ]);

        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $cid = $activeChannel->id;

        Setting::set($cid, 'screen_orientation', $validated['screen_orientation']);

        return redirect()->route('admin.settings')->with('success', 'Screen orientation updated.');
    }

    public function updateLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'file', 'mimes:png,svg,jpg,jpeg', 'max:2048'],
        ]);

        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $cid = $activeChannel->id;

        $oldPath = Setting::get($cid, 'logo_path');
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('logo');
        $filename = 'logo_'.$cid.'_'.time().'.'.$file->getClientOriginalExtension();
        $file->storeAs('logo', $filename, 'public');

        $logoPath = 'logo/'.$filename;
        Setting::set($cid, 'logo_path', $logoPath);

        $logoUrl = asset('storage/'.$logoPath);
        LogoUpdated::dispatch($logoUrl, $activeChannel->slug);

        return redirect()->route('admin.settings')->with('success', 'Logo updated.');
    }

    public function updateEventOverlay(EventOverlayRequest $request): RedirectResponse
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $cid = $activeChannel->id;

        $data = $request->validated();

        $overlayShow = isset($data['overlay_show']) ? '1' : '0';
        $data['overlay_show'] = $overlayShow;

        foreach (['overlay_show', 'overlay_location', 'overlay_subtitle', 'overlay_title', 'overlay_time', 'overlay_organizer'] as $key) {
            Setting::set($cid, $key, $data[$key] ?? '');
        }

        $overlayData = [
            'show' => $overlayShow === '1',
            'location' => $data['overlay_location'] ?? '',
            'subtitle' => $data['overlay_subtitle'] ?? '',
            'title' => $data['overlay_title'] ?? '',
            'time' => $data['overlay_time'] ?? '',
            'organizer' => $data['overlay_organizer'] ?? '',
        ];

        EventOverlayUpdated::dispatch($overlayData, $activeChannel->slug);

        return redirect()->route('admin.settings')->with('success', 'Event overlay updated.');
    }
}
