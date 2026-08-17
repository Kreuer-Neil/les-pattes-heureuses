<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Élise',
                'password' => 'password',
                'email_verified_at' => now(),
                'user_role_id' => UserRole::where('name', Roles::Admin)->first()->id,
            ]
        );

        // User::factory(10)->create();
    }
}
