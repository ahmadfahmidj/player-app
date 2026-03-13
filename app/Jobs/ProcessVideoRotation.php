<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoRotation implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 minutes max for video processing

    /**
     * Create a new job instance.
     */
    public function __construct(public Video $video)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('public');
        $originalPath = $this->video->path;

        if (! $disk->exists($originalPath)) {
            Log::error("Video Rotation: File not found at {$originalPath}");

            return;
        }

        $fullOriginalPath = $disk->path($originalPath);
        $tempFilename = 'rotated_'.time().'_'.basename($originalPath);
        $tempPath = 'videos/'.$tempFilename;
        $fullTempPath = $disk->path($tempPath);

        Log::info("Starting FFMpeg rotation on {$originalPath}...");

        // -vf "transpose=1" rotates 90 degrees clockwise
        // -c:a copy retains the original audio verbatim
        // -preset ultrafast prioritizes speed over compression
        // -threads 0 lets FFmpeg use all available CPU cores
        $cmd = sprintf(
            'ffmpeg -y -i %s -vf "transpose=1" -c:a copy -c:v libx264 -preset ultrafast -crf 23 -threads 0 %s 2>&1',
            escapeshellarg($fullOriginalPath),
            escapeshellarg($fullTempPath)
        );

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            Log::error("FFMpeg rotation failed for {$originalPath}", ['output' => implode("\n", $output)]);
            if ($disk->exists($tempPath)) {
                $disk->delete($tempPath);
            }

            return;
        }

        // Successfully transcoded. Replace original!
        $disk->delete($originalPath);
        $disk->move($tempPath, $originalPath);

        Log::info("Finished FFMpeg rotation on {$originalPath}.");
    }
}
