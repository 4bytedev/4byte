<?php

namespace Packages\Entry\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Packages\Entry\Models\Entry;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Entry\Models\Entry>
 */
class EntryFactory extends Factory
{
    protected $model = Entry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence();

        return [
            'slug'    => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'content' => $this->faker->paragraphs(rand(2, 5), true),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
