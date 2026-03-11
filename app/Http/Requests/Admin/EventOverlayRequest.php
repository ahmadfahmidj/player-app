<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventOverlayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'overlay_show' => ['nullable', 'string', 'in:on,1,true'],
            'overlay_location' => ['nullable', 'string', 'max:100'],
            'overlay_subtitle' => ['nullable', 'string', 'max:100'],
            'overlay_title' => ['nullable', 'string', 'max:200'],
            'overlay_time' => ['nullable', 'string', 'max:100'],
            'overlay_organizer' => ['nullable', 'string', 'max:200'],
        ];
    }
}
