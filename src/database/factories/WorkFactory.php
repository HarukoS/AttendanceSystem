<?php

namespace Database\Factories;

use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Work>
 */
class WorkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1,8),
            'date' => $this->faker->dateTimeBetween($startDate = '-1 week', $endDate = 'now'),
            'work_start' => $this->faker->time(),
            'work_end' => $this->faker->time(),
        ];
    }
}
