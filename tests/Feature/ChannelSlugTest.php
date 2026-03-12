<?php

use App\Models\Channel;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->channel = Channel::create([
        'name' => 'Main Channel',
        'slug' => 'main-channel',
        'is_main' => true,
    ]);
});

test('channel can be created with a custom slug', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('admin.channels.store'), [
        'name' => 'Lobby Screen',
        'slug' => 'lobby',
        'orientation' => 0,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('channels', [
        'name' => 'Lobby Screen',
        'slug' => 'lobby',
    ]);
});

test('channel slug is auto-generated from name when not provided', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('admin.channels.store'), [
        'name' => 'Ruang Tunggu Poli A',
        'slug' => '',
        'orientation' => 0,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('channels', [
        'name' => 'Ruang Tunggu Poli A',
        'slug' => 'ruang-tunggu-poli-a',
    ]);
});

test('channel slug can be updated', function () {
    $this->actingAs($this->user);

    $channel = Channel::create([
        'name' => 'Test Channel',
        'slug' => 'test-channel',
        'is_main' => false,
    ]);

    $response = $this->put(route('admin.channels.update', $channel), [
        'slug' => 'new-slug',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('channels', [
        'id' => $channel->id,
        'slug' => 'new-slug',
    ]);
});

test('channel slug must be unique', function () {
    $this->actingAs($this->user);

    $channel = Channel::create([
        'name' => 'Test Channel',
        'slug' => 'test-channel',
        'is_main' => false,
    ]);

    $response = $this->put(route('admin.channels.update', $channel), [
        'slug' => 'main-channel',
    ]);

    $response->assertSessionHasErrors('slug');
});

test('channel slug must be valid format', function () {
    $this->actingAs($this->user);

    $channel = Channel::create([
        'name' => 'Test Channel',
        'slug' => 'test-channel',
        'is_main' => false,
    ]);

    $response = $this->put(route('admin.channels.update', $channel), [
        'slug' => 'Invalid Slug!',
    ]);

    $response->assertSessionHasErrors('slug');
});
