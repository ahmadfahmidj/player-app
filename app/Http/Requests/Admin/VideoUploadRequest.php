<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'video' => ['required', 'file', 'mimetypes:video/mp4', 'max:512000'],
        ];
    }

    public function messages(): array
    {
        return [
            'video.mimetypes' => 'Only MP4 video files are accepted.',
            'video.max' => 'Video file may not be larger than 500MB.',
        ];
    }
}
