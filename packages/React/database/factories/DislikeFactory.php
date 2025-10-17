<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\React\Models\Dislike;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\React\Models\Dislike>
 */
class DislikeFactory extends Factory
{
    protected $model = Dislike::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'dislikeable_id' => null,
            'dislikeable_type' => null,
        ];
    }

    public function forModel($model)
    {
        return $this->state(function () use ($model) {
            return [
                'dislikeable_type' => get_class($model),
                'dislikeable_id' => $model->id,
            ];
        });
    }
}
