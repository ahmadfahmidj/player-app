<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageSlide extends Model
{
    protected $fillable = ['channel_id', 'title', 'filename', 'path', 'duration', 'order'];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'order' => 'integer',
        ];
    }

    public function channel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.ltrim($this->path, 'public/'));
    }
}
