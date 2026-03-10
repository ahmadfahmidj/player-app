<?php

namespace App\Http\Controllers\Admin;

use App\Events\LogoUpdated;
use App\Events\RunningTextUpdated;
use App\Http\Controllers\Controller;
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
        $runningText = Setting::get('running_text', '');
        $logoPath = Setting::get('logo_path');
        $logoUrl = $logoPath ? asset('storage/'.$logoPath) : null;

        return view('admin.settings', compact('runningText', 'logoUrl'));
    }

    public function updateRunningText(RunningTextRequest $request): RedirectResponse
    {
        $text = $request->string('text')->toString();
        Setting::set('running_text', $text);

        RunningTextUpdated::dispatch($text);

        return redirect()->route('admin.settings')->with('success', 'Running text updated.');
    }

    public function updateLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'file', 'mimes:png,svg,jpg,jpeg', 'max:2048'],
        ]);

        $oldPath = Setting::get('logo_path');
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('logo');
        $filename = 'logo.'.$file->getClientOriginalExtension();
        $file->storeAs('logo', $filename, 'public');

        $logoPath = 'logo/'.$filename;
        Setting::set('logo_path', $logoPath);

        $logoUrl = asset('storage/'.$logoPath);
        LogoUpdated::dispatch($logoUrl);

        return redirect()->route('admin.settings')->with('success', 'Logo updated.');
    }
}
