<?php

namespace Packages\Entry\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Entry\Models\Entry;
use Packages\Entry\Models\EntryComment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Entry\Models\EntryComment>
 */
class EntryCommentFactory extends Factory
{
    protected $model = EntryComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(),
            'parent_id' => (function () {
                $possibleParents = EntryComment::latest()->take(rand(3, 5))->get();

                return $possibleParents->isNotEmpty() && rand(1, 100) <= 30
                    ? $possibleParents->random()->id
                    : null;
            })(),
            'entry_id' => Entry::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
