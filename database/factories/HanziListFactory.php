<?php

namespace Database\Factories;

use App\Models\HanziList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HanziListFactory extends Factory
{
    protected $model = HanziList::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'list_reference' => $this->faker->unique()->bothify('list-####-????'),
            'list_name' => $this->faker->randomElement([
                'My Study List',
                'HSK Practice',
                'Daily Review',
                'Weekend Study',
                'Exam Preparation',
                'Favorite Characters',
                'Difficult Words',
                'Basic Vocabulary',
                'Advanced Terms',
                'Business Chinese'
            ]),
            'box_number' => $this->faker->numberBetween(3, 10),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    public function forUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    public function withBoxNumber($boxNumber): static
    {
        return $this->state(fn (array $attributes) => [
            'box_number' => $boxNumber,
        ]);
    }
}