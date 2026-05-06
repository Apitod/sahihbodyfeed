<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Creates (or ensures) the superadmin Spatie role exists,
 * then creates/updates the default superadmin user account.
 *
 * Safe to run multiple times (idempotent).
 *
 * Usage:  php artisan db:seed --class=SuperadminSeeder
 */
class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure the Spatie role exists
        $spatieRole = Role::firstOrCreate(
            ['name' => 'superadmin', 'guard_name' => 'web']
        );

        $this->command->info('Spatie role "superadmin": ' . ($spatieRole->wasRecentlyCreated ? 'CREATED' : 'already exists'));

        // 2. Create or retrieve the superadmin user
        $user = User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'password'  => bcrypt('superadmin123'),
                'role'      => 'superadmin',   // cached role column
                'is_active' => true,
            ]
        );

        // 3. Sync Spatie role (replaces any existing roles)
        $user->syncRoles(['superadmin']);

        $this->command->info('Superadmin user ready — username: superadmin, Spatie roles: ' . $user->getRoleNames()->implode(', '));
    }
}
