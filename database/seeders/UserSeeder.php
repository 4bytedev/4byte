<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Packages\React\Models\Follow;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(40)->create()->each(function ($user) {
            UserProfile::factory()->for($user)->create();
            Follow::factory(3)->forModel($user)->create();
        });
    }
}
