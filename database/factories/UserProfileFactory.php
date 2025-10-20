<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role'     => $this->faker->randomElement(['user', 'admin', 'moderator']),
            'bio'      => $this->faker->sentence(),
            'location' => $this->faker->city(),
            'website'  => $this->faker->url(),
            'socials'  => [
                'twitter'   => $this->faker->userName(),
                'instagram' => $this->faker->userName(),
            ],
        ];
    }
}
