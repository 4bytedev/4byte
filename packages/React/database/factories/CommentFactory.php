<?php

namespace Packages\React\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Packages\React\Models\Comment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\React\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content'          => $this->faker->paragraph(3),
            'user_id'          => User::inRandomOrder()->first()->id,
            'parent_id'        => null,
            'commentable_id'   => null,
            'commentable_type' => null,
        ];
    }

    public function forModel(Model $model): CommentFactory
    {
        return $this->state(function () use ($model) {
            return [
                'commentable_id'   => $model->id, /* @phpstan-ignore-line */
                'commentable_type' => get_class($model),
            ];
        });
    }
}
