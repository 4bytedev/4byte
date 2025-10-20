<?php

namespace Packages\Tag\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Category\Models\Category;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Tag\Models\TagProfile>
 */
class TagProfileFactory extends Factory
{
    protected $model = TagProfile::class;

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
            'tag_id'      => Tag::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
