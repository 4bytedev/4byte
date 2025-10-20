<?php

namespace Packages\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\React\Models\Follow;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory(10)->create()->each(function ($category) {
            CategoryProfile::factory()->for($category)->create();
            Follow::factory(3)->forModel($category)->create();
        });
    }
}
