<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'location' => ['nullable', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:100'],
            'time_display' => ['nullable', 'string', 'max:100'],
            'organizer' => ['nullable', 'string', 'max:200'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'channel_ids' => ['required', 'array', 'min:1'],
            'channel_ids.*' => ['integer', 'exists:channels,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ends_at.after' => 'The end time must be after the start time.',
            'channel_ids.required' => 'Please select at least one channel.',
            'channel_ids.min' => 'Please select at least one channel.',
        ];
    }
}
