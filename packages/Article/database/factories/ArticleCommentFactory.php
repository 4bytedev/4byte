<?php

namespace Packages\Article\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Article\Models\Article;
use Packages\Article\Models\ArticleComment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Article\Models\ArticleComment>
 */
class ArticleCommentFactory extends Factory
{
    protected $model = ArticleComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(),
            'parent_id' => (function () {
                $possibleParents = ArticleComment::latest()->take(rand(3, 5))->get();

                return $possibleParents->isNotEmpty() && rand(1, 100) <= 30
                    ? $possibleParents->random()->id
                    : null;
            })(),
            'article_id' => Article::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
