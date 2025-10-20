<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'view-creator-card']);
        Permission::firstOrCreate(['name' => 'update-account']);
        Permission::firstOrCreate(['name' => 'update-password']);
        Permission::firstOrCreate(['name' => 'update-profile']);
        Permission::firstOrCreate(['name' => 'delete-sessions']);
        Permission::firstOrCreate(['name' => 'view-notification']);
        Permission::firstOrCreate(['name' => 'view-panel']);
    }
}
