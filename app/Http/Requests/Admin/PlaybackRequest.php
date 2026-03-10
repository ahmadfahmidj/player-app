<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PlaybackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video_id' => ['sometimes', 'integer', 'exists:videos,id'],
            'position' => ['sometimes', 'numeric', 'min:0'],
            'loop_mode' => ['sometimes', 'string', 'in:none,single,playlist'],
        ];
    }
}
