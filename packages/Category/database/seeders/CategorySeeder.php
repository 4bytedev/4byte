<?php

namespace Packages\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryFollow;
use Packages\Category\Models\CategoryProfile;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory(10)->create()->each(function ($category) {
            CategoryProfile::factory()->for($category)->create();
        });

        CategoryFollow::factory(20)->create();
    }
}
