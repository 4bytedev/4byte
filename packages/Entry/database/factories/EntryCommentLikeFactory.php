<?php

namespace Packages\Entry\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Entry\Models\Entry;
use Packages\Entry\Models\EntryComment;
use Packages\Entry\Models\EntryCommentLike;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Entry\Models\EntryCommentLike>
 */
class EntryCommentLikeFactory extends Factory
{
    protected $model = EntryCommentLike::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'entry_id' => Entry::inRandomOrder()->first()->id,
            'comment_id' => EntryComment::inRandomOrder()->first()->id,
        ];
    }
}
