<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserRolesSeeder::class,
            VaccinesSeeder::class,
            AnimalOptionsSeeder::class,
            UsersSeeder::class,
            VolunteersSeeder::class,
            AnimalsSeeder::class,
            AdoptionRequestsSeeder::class,
        ]);
    }
}
