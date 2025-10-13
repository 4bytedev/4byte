<?php

namespace Packages\Article\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Article\Models\Article;
use Packages\Article\Models\ArticleSave;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Article\Models\ArticleSave>
 */
class ArticleSaveFactory extends Factory
{
    protected $model = ArticleSave::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'article_id' => Article::inRandomOrder()->first()->id,
        ];
    }
}
