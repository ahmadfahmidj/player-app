<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoRotation;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $videos = $activeChannel ? $activeChannel->videos()->orderByPivot('order')->get() : collect();
        $allVideos = Video::query()->orderBy('title')->get();

        return view('admin.videos', compact('videos', 'allVideos', 'activeChannel'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');

        if (! $activeChannel) {
            return redirect()->back()->withErrors(['channel' => 'No active channel found.']);
        }

        // Check if we are adding an existing video
        if ($request->filled('existing_video_id')) {
            $request->validate(['existing_video_id' => 'required|exists:videos,id']);
            $video = Video::find($request->input('existing_video_id'));

            $maxOrder = $activeChannel->videos()->max('channel_video.order') ?? 0;
            if (! $activeChannel->videos()->where('video_id', $video->id)->exists()) {
                $activeChannel->videos()->attach($video->id, ['order' => $maxOrder + 1]);
            }

            return redirect()->route('admin.videos')->with('success', __('Video added to playlist.'));
        }

        // Otherwise uploading a new video
        $request->validate([
            'title' => 'nullable|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4|max:500000',
        ]);

        $file = $request->file('video');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('videos', $filename, 'public');

        $title = $request->filled('title')
            ? $request->string('title')
            : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $duration = 0;
        if (function_exists('shell_exec')) {
            $fullPath = Storage::disk('public')->path($path);
            $output = shell_exec('ffprobe -v quiet -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 '.escapeshellarg($fullPath).' 2>/dev/null');
            if ($output) {
                $duration = (int) round((float) trim($output));
            }
        }

        $maxOrder = $activeChannel->videos()->max('channel_video.order') ?? 0;

        $video = Video::create([
            'title' => $title,
            'filename' => $filename,
            'path' => 'videos/'.$filename,
            'duration' => $duration,
        ]);

        $activeChannel->videos()->attach($video->id, ['order' => $maxOrder + 1]);

        if ($request->boolean('rotate')) {
            ProcessVideoRotation::dispatch($video);
        }

        return redirect()->route('admin.videos')->with('success', __('Video uploaded and added to playlist.'));
    }

    public function update(Request $request, Video $video): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'rotate' => 'nullable|boolean',
        ]);

        $video->update(['title' => $request->string('title')]);

        if ($request->boolean('rotate')) {
            ProcessVideoRotation::dispatch($video);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'title' => $video->title]);
        }

        return redirect()->route('admin.videos')->with('success', __('Video updated.'));
    }

    public function destroy(Video $video): RedirectResponse
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');

        // Only detach from current channel
        $activeChannel->videos()->detach($video->id);

        // Optional: If you want to delete the file if no channels use it anymore
        // if ($video->channels()->count() === 0) {
        //     Storage::disk('public')->delete('videos/'.$video->filename);
        //     $video->delete();
        // }

        return redirect()->route('admin.videos')->with('success', __('Video removed from playlist.'));
    }

    public function forceDestroy(Video $video): RedirectResponse
    {
        // Detach from all channels
        $video->channels()->detach();

        // Delete the file from storage
        Storage::disk('public')->delete($video->path);

        // Delete the database record
        $video->delete();

        return redirect()->route('admin.videos')->with('success', __('Video permanently deleted.'));
    }

    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $orderData = $request->input('order'); // array of video IDs

        foreach ($orderData as $position => $id) {
            $activeChannel->videos()->updateExistingPivot($id, ['order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}
