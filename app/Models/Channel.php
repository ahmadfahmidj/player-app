<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['name', 'slug', 'is_main', 'orientation'];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function videos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Video::class)->withPivot('order')->orderByPivot('order');
    }

    public function broadcastState(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BroadcastState::class);
    }

    public function settings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Setting::class);
    }
}
