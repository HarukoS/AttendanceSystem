<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Work;

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
        $date = $this->faker->dateTimeThisMonth;

        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'date' => $date->format('Y-m-d'),
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
        ];
    }
}
