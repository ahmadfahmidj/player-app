<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->numerify('##########').'_'.fake()->word().'.mp4';

        return [
            'title' => fake()->sentence(3),
            'filename' => $filename,
            'path' => 'videos/'.$filename,
            'duration' => fake()->numberBetween(30, 3600),
            'order' => fake()->numberBetween(1, 100),
        ];
    }
}
