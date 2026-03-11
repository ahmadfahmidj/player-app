<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastState extends Model
{
    public $timestamps = false;

    protected $table = 'broadcast_state';

    protected $fillable = [
        'channel_id',
        'current_video_id',
        'current_position',
        'is_playing',
        'loop_mode',
        'started_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'channel_id' => 'integer',
            'current_position' => 'float',
            'is_playing' => 'boolean',
            'started_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public static function current(int $channelId): static
    {
        return static::firstOrCreate(['channel_id' => $channelId]);
    }

    public function channel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function video(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Video::class, 'current_video_id');
    }

    public function calculateCurrentPosition(): float
    {
        if (! $this->is_playing || ! $this->started_at) {
            return (float) $this->current_position;
        }

        return (float) $this->current_position + now()->diffInSeconds($this->started_at);
    }
}
