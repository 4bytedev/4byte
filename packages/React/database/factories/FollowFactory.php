<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\React\Models\Follow;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\React\Models\Follow>
 */
class FollowFactory extends Factory
{
    protected $model = Follow::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'follower_id' => User::inRandomOrder()->first()->id,
            'followable_id' => null,
            'followable_type' => null,
        ];
    }

    public function forModel($model)
    {
        return $this->state(function () use ($model) {
            return [
                'followable_id' => $model->id,
                'followable_type' => get_class($model),
            ];
        });
    }
}
