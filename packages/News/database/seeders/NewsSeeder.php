<?php

namespace Packages\News\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\News\Models\News;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        News::factory(20)->create();
    }
}
