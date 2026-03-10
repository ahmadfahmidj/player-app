<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerStateResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $video = $this->video;
        $logoPath = Setting::get('logo_path');

        return [
            'current_video_id' => $this->current_video_id,
            'video_url' => $video ? asset('storage/videos/' . $video->filename) : null,
            'video_title' => $video?->title,
            'current_position' => $this->calculateCurrentPosition(),
            'is_playing' => $this->is_playing,
            'loop_mode' => $this->loop_mode,
            'running_text' => Setting::get('running_text', 'Selamat datang di Rumah Sakit'),
            'logo_url' => $logoPath ? asset('storage/' . $logoPath) : null,
        ];
    }
}
