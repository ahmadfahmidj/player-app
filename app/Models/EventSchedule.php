<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\EventScheduleFactory> */
    use HasFactory;

    protected $fillable = ['title', 'location', 'subtitle', 'time_display', 'organizer', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function channels(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Channel::class);
    }

    /** @param Builder<EventSchedule> $query */
    public function scopeCurrentlyRunning(Builder $query): void
    {
        $now = now();
        $query->where('starts_at', '<=', $now)->where('ends_at', '>=', $now);
    }

    /**
     * Events starting within the given minutes from now.
     *
     * @param  Builder<EventSchedule>  $query
     */
    public function scopeUpcomingWithin(Builder $query, int $minutes = 60): void
    {
        $now = now();
        $query->whereBetween('starts_at', [$now, $now->copy()->addMinutes($minutes)])
            ->where('ends_at', '>=', $now);
    }
}
