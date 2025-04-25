<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WordGuess;
use App\Models\User;
use App\Models\DictionaryZhHans;

class WordGuessFactory extends Factory
{
    protected $model = WordGuess::class;

    public function definition(): array
    {
        return [
            // 'user_id' => User::inRandomOrder()->first()?->id ?? 1,
            'user_id' => 1,
            'dictionary_zh_hans_id' => DictionaryZhHans::inRandomOrder()->first()?->id ?? 1,
            'is_correct' => $this->faker->boolean(),
            'guessed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
