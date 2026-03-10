<?php

use App\Support\ViteLegacy;

it('renders nomodule script tags for legacy entrypoints', function () {
    $html = ViteLegacy::scripts(['resources/js/player.js'])->toHtml();

    expect($html)
        ->toContain('<script nomodule src="/build/assets/polyfills-legacy-')
        ->toContain('<script nomodule src="/build/assets/pusher-legacy-')
        ->toContain('<script nomodule src="/build/assets/player-legacy-');
});

it('renders polyfills before other scripts', function () {
    $html = ViteLegacy::scripts(['resources/js/player.js'])->toHtml();

    $polyfillPos = strpos($html, 'polyfills-legacy-');
    $playerPos = strpos($html, 'player-legacy-');

    expect($polyfillPos)->toBeLessThan($playerPos);
});

it('returns empty html for non-existent entrypoints', function () {
    $html = ViteLegacy::scripts(['resources/js/nonexistent.js'])->toHtml();

    expect($html)->not->toContain('nonexistent');
});

it('includes legacy scripts in the player page response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('nomodule', false);
    $response->assertSee('player-legacy-', false);
});
