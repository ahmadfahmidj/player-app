<?php

test('queue config defaults to redis', function () {
    expect(config('queue.connections.redis.driver'))->toBe('redis');
});

test('horizon service provider is registered', function () {
    expect(app()->getProviders(\Laravel\Horizon\HorizonServiceProvider::class))->not->toBeEmpty();
});

test('horizon supervisor uses redis connection', function () {
    expect(config('horizon.defaults.supervisor-1.connection'))->toBe('redis');
});

test('horizon snapshot is scheduled', function () {
    $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
    $commands = collect($schedule->events())->map(fn ($event) => $event->command);

    expect($commands->filter(fn ($cmd) => str_contains($cmd ?? '', 'horizon:snapshot')))->not->toBeEmpty();
});
