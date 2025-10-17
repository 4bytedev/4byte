<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\React\Models\Save;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\React\Models\Save>
 */
class SaveFactory extends Factory
{
    protected $model = Save::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'saveable_id' => null,
            'saveable_type' => null,
        ];
    }

    public function forModel($model)
    {
        return $this->state(function () use ($model) {
            return [
                'saveable_id' => $model->id,
                'saveable_type' => get_class($model),
            ];
        });
    }
}
