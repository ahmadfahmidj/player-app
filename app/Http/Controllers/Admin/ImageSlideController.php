<?php

namespace App\Http\Controllers\Admin;

use App\Events\ImageSlidesUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImageSlideUpdateRequest;
use App\Http\Requests\Admin\ImageSlideUploadRequest;
use App\Jobs\ProcessImageRotation;
use App\Models\ImageSlide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ImageSlideController extends Controller
{
    public function index(): View
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $slides = $activeChannel
            ? ImageSlide::where('channel_id', $activeChannel->id)->orderBy('order')->get()
            : collect();

        return view('admin.image-slides', compact('slides', 'activeChannel'));
    }

    public function store(ImageSlideUploadRequest $request): RedirectResponse
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');

        if (! $activeChannel) {
            return redirect()->back()->withErrors(['channel' => 'No active channel found.']);
        }

        $file = $request->file('image');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('slides', $filename, 'public');

        $title = $request->filled('title')
            ? $request->string('title')->toString()
            : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $maxOrder = ImageSlide::where('channel_id', $activeChannel->id)->max('order') ?? 0;

        $slide = ImageSlide::create([
            'channel_id' => $activeChannel->id,
            'title' => $title,
            'filename' => $filename,
            'path' => 'slides/'.$filename,
            'duration' => $request->integer('duration'),
            'order' => $maxOrder + 1,
        ]);

        if ($request->boolean('rotate')) {
            ProcessImageRotation::dispatch($slide);
        }

        $this->broadcastSlides($activeChannel);

        return redirect()->route('admin.image-slides')->with('success', __('Image slide uploaded.'));
    }

    public function update(ImageSlideUpdateRequest $request, ImageSlide $imageSlide): RedirectResponse|JsonResponse
    {
        $imageSlide->update([
            'title' => $request->input('title'),
            'duration' => $request->integer('duration'),
        ]);

        if ($request->boolean('rotate')) {
            ProcessImageRotation::dispatch($imageSlide);
        }

        $this->broadcastSlides($imageSlide->channel);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.image-slides')->with('success', __('Image slide updated.'));
    }

    public function destroy(ImageSlide $imageSlide): RedirectResponse
    {
        $channel = $imageSlide->channel;

        Storage::disk('public')->delete($imageSlide->path);
        $imageSlide->delete();

        $this->broadcastSlides($channel);

        return redirect()->route('admin.image-slides')->with('success', __('Image slide deleted.'));
    }

    public function reorder(Request $request): JsonResponse
    {
        $activeChannel = \Illuminate\Support\Facades\View::shared('activeChannel');
        $orderData = $request->input('order'); // array of image slide IDs

        foreach ($orderData as $position => $id) {
            ImageSlide::where('id', $id)
                ->where('channel_id', $activeChannel->id)
                ->update(['order' => $position + 1]);
        }

        $this->broadcastSlides($activeChannel);

        return response()->json(['success' => true]);
    }

    private function broadcastSlides(\App\Models\Channel $channel): void
    {
        $slides = ImageSlide::where('channel_id', $channel->id)
            ->orderBy('order')
            ->get()
            ->map(fn (ImageSlide $s) => ['url' => $s->url, 'duration' => $s->duration])
            ->values()
            ->all();

        ImageSlidesUpdated::dispatch($slides, $channel->slug);
    }
}
