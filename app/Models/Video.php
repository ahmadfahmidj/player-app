<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'filename', 'path', 'duration', 'order'];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'order' => 'integer',
        ];
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.ltrim($this->path, 'public/'));
    }
}
