<?php

namespace Packages\Entry\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Entry\Models\Entry;
use Packages\React\Models\Comment;
use Packages\React\Models\Dislike;
use Packages\React\Models\Like;
use Packages\React\Models\Save;

class EntrySeeder extends Seeder
{
    public function run(): void
    {
        Entry::factory(20)->create()->each(function (Entry $entry) {
            Like::factory(3)->forModel($entry)->create();
            Dislike::factory(3)->forModel($entry)->create();
            Save::factory(3)->forModel($entry)->create();
            Comment::factory(5)->forModel($entry)->create();
        });
    }
}
