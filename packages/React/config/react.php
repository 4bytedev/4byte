<?php

return [
    'classes' => [
        'article'  => Packages\Article\Models\Article::class,
        'entry'    => Packages\Entry\Models\Entry::class,
        'category' => Packages\Category\Models\Category::class,
        'tag'      => Packages\Tag\Models\Tag::class,
        'user'     => App\Models\User::class,
        'comment'  => Packages\React\Models\Comment::class,
    ],

    'callbacks' => [
        'article'  => Packages\Article\Services\ArticleService::class,
        'entry'    => Packages\Entry\Services\EntryService::class,
        'category' => Packages\Category\Services\CategoryService::class,
        'tag'      => Packages\Tag\Services\TagService::class,
        'user'     => App\Services\UserService::class,
        'comment'  => "self",
    ],
];
