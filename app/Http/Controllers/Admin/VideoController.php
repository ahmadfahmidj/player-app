<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VideoReorderRequest;
use App\Http\Requests\Admin\VideoUploadRequest;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        $videos = Video::query()->orderBy('order')->get();

        return view('admin.videos', compact('videos'));
    }

    public function store(VideoUploadRequest $request): RedirectResponse
    {
        $file = $request->file('video');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('videos', $filename, 'public');

        $duration = 0;
        if (function_exists('shell_exec')) {
            $fullPath = Storage::disk('public')->path($path);
            $output = shell_exec('ffprobe -v quiet -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 '.escapeshellarg($fullPath).' 2>/dev/null');
            if ($output) {
                $duration = (int) round((float) trim($output));
            }
        }

        $maxOrder = Video::query()->max('order') ?? 0;

        Video::create([
            'title' => $request->string('title'),
            'filename' => $filename,
            'path' => 'videos/'.$filename,
            'duration' => $duration,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.videos')->with('success', 'Video uploaded successfully.');
    }

    public function destroy(Video $video): RedirectResponse
    {
        Storage::disk('public')->delete('videos/'.$video->filename);
        $video->delete();

        return redirect()->route('admin.videos')->with('success', 'Video deleted successfully.');
    }

    public function reorder(VideoReorderRequest $request): \Illuminate\Http\JsonResponse
    {
        foreach ($request->input('order') as $position => $id) {
            Video::where('id', $id)->update(['order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}
