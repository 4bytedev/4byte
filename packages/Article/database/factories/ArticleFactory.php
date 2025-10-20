<?php

namespace Packages\Article\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Packages\Article\Models\Article;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Article\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence();

        return [
            'title'   => $title,
            'slug'    => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(5, true),
            'status'  => $this->faker->randomElement(['DRAFT', 'PUBLISHED', 'PENDING']),
            'sources' => collect(range(0, rand(0, 5)))->map(function () {
                return [
                    'url'  => $this->faker->domainName(),
                    'date' => $this->faker->dateTimeBetween('-6 months', '+6 months'),
                ];
            })->toArray(),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'user_id'      => User::inRandomOrder()->first()->id,
        ];
    }
}
