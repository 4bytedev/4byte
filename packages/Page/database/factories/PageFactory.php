<?php

namespace Packages\Page\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Packages\Page\Models\Page;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Page\Models\Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence();

        return [
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'excerpt'      => $this->faker->paragraph(2),
            'content'      => $this->faker->paragraphs(5, true),
            'status'       => $this->faker->randomElement(['draft', 'published', 'archived']),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'user_id'      => User::inRandomOrder()->first()->id,
        ];
    }
}
