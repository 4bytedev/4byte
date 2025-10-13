<?php

namespace Packages\Tag\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagFollow;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Tag\Models\TagFollow>
 */
class TagFollowFactory extends Factory
{
    protected $model = TagFollow::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'tag_id' => Tag::inRandomOrder()->first()->id,
        ];
    }
}
