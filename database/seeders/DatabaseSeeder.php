<?php

namespace Database\Seeders;

use App\Enum\AnimalBreeds\BirdBreed;
use App\Enum\AnimalBreeds\CatBreed;
use App\Enum\AnimalBreeds\DogBreed;
use App\Enum\AnimalBreeds\HorseBreed;
use App\Enum\AnimalBreeds\ReptileBreeds;
use App\Enum\AnimalSpecie;
use App\Enum\Status;
use App\Models\Animal\AnimalStatus;
use App\Models\Animal\Breed;
use App\Models\Animal\Specie;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        foreach (Status::cases() as $animalStatus) {
            AnimalStatus::factory([
                    'name' => $animalStatus->value,
                ]
            )->create();
        }


        foreach (AnimalSpecie::cases() as $animalSpecie) {
            Specie::factory([
                    'name' => $animalSpecie->value,
                ]
            )->create();
        }

        /*
         * Animal breeds
         */
        // Dogs
        foreach (DogBreed::cases() as $breed) {
            Breed::factory([
                    'name' => $breed->value,
                    'specie_id' => 1
                ]
            )->create();
        }

        // Cats
        foreach (CatBreed::cases() as $breed) {
            Breed::factory([
                    'name' => $breed->value,
                    'specie_id' => 2
                ]
            )->create();
        }

        // Birds
        foreach (BirdBreed::cases() as $breed) {
            Breed::factory([
                    'name' => $breed->value,
                    'specie_id' => 3
                ]
            )->create();
        }

        // Horses
        foreach (HorseBreed::cases() as $breed) {
            Breed::factory([
                    'name' => $breed->value,
                    'specie_id' => 4
                ]
            )->create();
        }

        // Reptiles
        foreach (ReptileBreeds::cases() as $breed) {
            Breed::factory([
                    'name' => $breed->value,
                    'specie_id' => 5
                ]
            )->create();
        }


    }
}
