<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoOptimization implements ShouldQueue
{
    use Queueable;

    public $timeout = 900; // 15 minutes max for full re-encode

    /**
     * Create a new job instance.
     */
    public function __construct(public Video $video, public bool $rotate = false)
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
            Log::error("Video Optimization: File not found at {$originalPath}");

            return;
        }

        $fullOriginalPath = $disk->path($originalPath);
        $tempFilename = 'optimized_'.time().'_'.basename($originalPath);
        $tempPath = 'videos/'.$tempFilename;
        $fullTempPath = $disk->path($tempPath);

        Log::info("Starting FFMpeg optimization on {$originalPath}...");

        // -movflags +faststart moves the moov atom to the start for streaming
        // -profile:v baseline -level:v 3.1 ensures compatibility with old Android hardware decoders
        // -pix_fmt yuv420p ensures broad device compatibility
        // -c:a aac -b:a 128k re-encodes audio to a widely supported format
        $vfFlag = $this->rotate ? '-vf "transpose=1"' : '';

        $cmd = sprintf(
            'ffmpeg -y -i %s %s -c:v libx264 -profile:v baseline -level:v 3.1 -pix_fmt yuv420p -preset ultrafast -crf 23 -threads 0 -movflags +faststart -c:a aac -b:a 128k %s 2>&1',
            escapeshellarg($fullOriginalPath),
            $vfFlag,
            escapeshellarg($fullTempPath)
        );

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            Log::error("FFMpeg optimization failed for {$originalPath}", ['output' => implode("\n", $output)]);
            if ($disk->exists($tempPath)) {
                $disk->delete($tempPath);
            }

            return;
        }

        // Successfully transcoded. Replace original!
        $disk->delete($originalPath);
        $disk->move($tempPath, $originalPath);

        $this->video->update(['is_optimized' => true]);

        Log::info("Finished FFMpeg optimization on {$originalPath}.");
    }
}
