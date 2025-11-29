<?php

namespace Database\Factories;

use App\Models\ExperienceHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceHistoryFactory extends Factory
{
    protected $model = ExperienceHistory::class;

    public function definition(): array
    {
        $reasons = [
            'Correct word guess',
            'Completed lesson',
            'Daily login bonus',
            'Perfect quiz score',
            'Streak bonus',
            'Achievement unlocked',
            'Practice session completed',
            'Hanzi writing practice',
            'Flip card game completed',
            'Weekly goal achieved'
        ];

        return [
            'user_id' => User::factory(),
            'change' => $this->faker->randomElement([5, 10, 15, 20, 25, 50]),
            'reason' => $this->faker->randomElement($reasons),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now()
        ];
    }

    public function positive(int $amount = null): static
    {
        return $this->state(fn (array $attributes) => [
            'change' => $amount ?? $this->faker->numberBetween(5, 50),
        ]);
    }

    public function negative(int $amount = null): static
    {
        return $this->state(fn (array $attributes) => [
            'change' => -($amount ?? $this->faker->numberBetween(1, 10)),
        ]);
    }

    public function forUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}