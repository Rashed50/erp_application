<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * Credentials are read from the environment so real credentials are never
     * committed to source control. Override SUPER_ADMIN_EMAIL/SUPER_ADMIN_PASSWORD
     * in .env before seeding a non-local environment.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'superadmin@example.com')],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles(['Super Admin']);
    }
}
