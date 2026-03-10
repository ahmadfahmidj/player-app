<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(): View
    {
        $runningText = Setting::get('running_text', 'Selamat datang di Rumah Sakit');
        $logoPath = Setting::get('logo_path');
        $logoUrl = $logoPath ? asset('storage/'.$logoPath) : null;

        return view('player', compact('runningText', 'logoUrl'));
    }

    public function jadwal(): View
    {
        $runningText = Setting::get('running_text', 'Selamat datang di Rumah Sakit');
        $logoPath = Setting::get('logo_path');
        $logoUrl = $logoPath ? asset('storage/'.$logoPath) : null;

        return view('player-jadwal', compact('runningText', 'logoUrl'));
    }
}
