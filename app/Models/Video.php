<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'filename', 'path', 'duration'];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
        ];
    }

    public function channels(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Channel::class)->withPivot('order');
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv($this->duration, 3600);
        $minutes = intdiv($this->duration % 3600, 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.ltrim($this->path, 'public/'));
    }
}
