<?php

namespace Packages\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Page\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::factory(10)->create();
    }
}
