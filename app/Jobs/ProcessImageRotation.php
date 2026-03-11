<?php

namespace App\Jobs;

use App\Models\ImageSlide;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessImageRotation implements ShouldQueue
{
    use Queueable;

    public $timeout = 120;

    public function __construct(public ImageSlide $imageSlide) {}

    public function handle(): void
    {
        $disk = Storage::disk('public');
        $path = $this->imageSlide->path;

        if (! $disk->exists($path)) {
            Log::error("Image Rotation: File not found at {$path}");

            return;
        }

        $fullPath = $disk->path($path);
        $mime = mime_content_type($fullPath);

        Log::info("Starting image rotation on {$path}...");

        $src = match (true) {
            str_contains($mime, 'jpeg') => imagecreatefromjpeg($fullPath),
            str_contains($mime, 'png') => imagecreatefrompng($fullPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($fullPath),
            default => null,
        };

        if ($src === null || $src === false) {
            Log::error("Image Rotation: Unsupported image type '{$mime}' for {$path}");

            return;
        }

        // imagerotate uses counter-clockwise degrees; -90 = 90° clockwise
        $rotated = imagerotate($src, -90, 0);
        imagedestroy($src);

        if ($rotated === false) {
            Log::error("Image Rotation: imagerotate failed for {$path}");

            return;
        }

        $result = match (true) {
            str_contains($mime, 'jpeg') => imagejpeg($rotated, $fullPath, 90),
            str_contains($mime, 'png') => imagepng($rotated, $fullPath),
            str_contains($mime, 'webp') => imagewebp($rotated, $fullPath, 90),
            default => false,
        };

        imagedestroy($rotated);

        if (! $result) {
            Log::error("Image Rotation: Failed to save rotated image at {$path}");

            return;
        }

        Log::info("Finished image rotation on {$path}.");
    }
}
