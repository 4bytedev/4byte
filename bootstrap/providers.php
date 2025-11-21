<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Packages\Article\ArticleProvider::class,
    Packages\Category\CategoryProvider::class,
    Packages\News\NewsProvider::class,
    Packages\Page\PageProvider::class,
    Packages\Recommend\RecommendProvider::class,
    Packages\Tag\TagProvider::class,
    Packages\Entry\EntryProvider::class,
    Packages\React\ReactProvider::class,
    Packages\Search\SearchProvider::class,
    Packages\Course\CourseProvider::class
];
