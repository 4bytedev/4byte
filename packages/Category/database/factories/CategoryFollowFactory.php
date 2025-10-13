<?php

namespace Packages\Category\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryFollow;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Category\Models\CategoryFollow>
 */
class CategoryFollowFactory extends Factory
{
    protected $model = CategoryFollow::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
