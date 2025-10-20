<?php

namespace Packages\Category\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Category\Models\CategoryProfile>
 */
class CategoryProfileFactory extends Factory
{
    protected $model = CategoryProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->paragraph(),
            'color'       => $this->faker->hexColor(),
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
