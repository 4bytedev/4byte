<?php

namespace Packages\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Article\Models\Article;
use Packages\Article\Models\ArticleComment;
use Packages\Article\Models\ArticleCommentLike;
use Packages\Article\Models\ArticleDislike;
use Packages\Article\Models\ArticleLike;
use Packages\Article\Models\ArticleSave;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::factory(20)->create();

        ArticleComment::factory(50)->create();

        ArticleLike::factory(50)->create();

        ArticleCommentLike::factory(30)->create();

        ArticleDislike::factory(20)->create();

        ArticleSave::factory(20)->create();
    }
}
