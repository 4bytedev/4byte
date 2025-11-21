<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Packages\React\Models\Follow;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::withoutEvents(function () {

            $adminUser = User::create([
                'name'     => 'Admin',
                'username' => 'admin',
                'email'    => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);

            $adminUser->syncRoles(Role::where('name', 'Super Admin')->first());

            $userRole = Role::where('name', 'User')->first();

            User::factory(40)->create()->each(function (User $user) use ($userRole) {
                $user->syncRoles($userRole);
                UserProfile::factory()->for($user)->create();
                Follow::factory(3)->forModel($user)->create();
            });

        });
    }
}
