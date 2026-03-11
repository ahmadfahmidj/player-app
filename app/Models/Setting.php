<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['channel_id', 'key', 'value'];

    protected function casts(): array
    {
        return [
            'channel_id' => 'integer',
        ];
    }

    public static function get(int $channelId, string $key, mixed $default = null): mixed
    {
        return static::where('channel_id', $channelId)->where('key', $key)->value('value') ?? $default;
    }

    public static function set(int $channelId, string $key, mixed $value): void
    {
        static::updateOrCreate(['channel_id' => $channelId, 'key' => $key], ['value' => $value]);
    }
}
