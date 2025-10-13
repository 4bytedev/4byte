<?php

namespace Packages\Entry\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Entry\Models\Entry;
use Packages\Entry\Models\EntrySave;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Entry\Models\EntrySave>
 */
class EntrySaveFactory extends Factory
{
    protected $model = EntrySave::class;

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
        ];
    }
}
