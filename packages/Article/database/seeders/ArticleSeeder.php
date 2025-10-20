<?php

namespace Packages\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Article\Models\Article;
use Packages\React\Models\Comment;
use Packages\React\Models\Dislike;
use Packages\React\Models\Like;
use Packages\React\Models\Save;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::factory(20)->create()->each(function (Article $article) {
            Like::factory(10)->forModel($article)->create();
            Dislike::factory(10)->forModel($article)->create();
            Save::factory(10)->forModel($article)->create();
            Comment::factory(20)->forModel($article)->create();
        });
    }
}
