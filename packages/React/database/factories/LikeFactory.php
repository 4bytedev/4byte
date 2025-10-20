<?php

namespace Packages\React\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Packages\React\Models\Like;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\React\Models\Like>
 */
class LikeFactory extends Factory
{
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => User::inRandomOrder()->first()->id,
            'likeable_id'   => null,
            'likeable_type' => null,
        ];
    }

    public function forModel(Model $model): LikeFactory
    {
        return $this->state(function () use ($model) {
            return [
                'likeable_id'   => $model->id, /* @phpstan-ignore-line */
                'likeable_type' => get_class($model),
            ];
        });
    }
}
