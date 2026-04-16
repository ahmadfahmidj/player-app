<?php

namespace App\Console\Commands;

use App\Jobs\ProcessVideoOptimization;
use App\Models\Video;
use Illuminate\Console\Command;

class OptimizeVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch optimization jobs for all unoptimized videos';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $videos = Video::query()->where('is_optimized', false)->get();

        $videos->each(fn (Video $video) => ProcessVideoOptimization::dispatch($video));

        $this->info("Dispatched optimization for {$videos->count()} video(s).");
    }
}
