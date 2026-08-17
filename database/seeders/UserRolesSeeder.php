<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Roles::cases() as $role) {
            UserRole::firstOrCreate(['name' => $role->value]);
        }
    }
}
