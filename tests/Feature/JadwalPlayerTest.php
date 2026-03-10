<?php

test('jadwal player page returns a successful response', function () {
    $response = $this->get(route('player.jadwal'));

    $response->assertOk();
});

test('jadwal player page contains video element', function () {
    $response = $this->get(route('player.jadwal'));

    $response->assertSee('my-video', false);
    $response->assertSee('ticker-text', false);
});
