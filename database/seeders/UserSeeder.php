<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserFollow;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(40)->create()->each(function ($user) {
            UserProfile::factory()->for($user)->create();
        });

        UserFollow::factory(20)->create();
    }
}
