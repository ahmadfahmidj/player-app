<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventSchedule>
 */
class EventScheduleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('now', '+7 days');

        return [
            'title' => fake()->sentence(3),
            'location' => fake()->city(),
            'subtitle' => fake()->sentence(4),
            'time_display' => fake()->time('H:i').' - '.fake()->time('H:i'),
            'organizer' => fake()->company(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 hours'),
        ];
    }

    public function currentlyRunning(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subMinutes(30),
            'ends_at' => now()->addMinutes(90),
        ]);
    }

    public function upcomingIn(int $minutes): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->addMinutes($minutes),
            'ends_at' => now()->addMinutes($minutes + 120),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subHours(4),
            'ends_at' => now()->subHours(2),
        ]);
    }
}
