<?php

test('player page returns a successful response', function () {
    $response = $this->get(route('player'));

    $response->assertOk();
});
