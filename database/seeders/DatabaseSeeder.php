<?php

namespace Database\Seeders;

use App\Enums\Animals\Breeds\BirdBreed;
use App\Enums\Animals\Breeds\CatBreed;
use App\Enums\Animals\Breeds\DogBreed;
use App\Enums\Animals\Breeds\HorseBreed;
use App\Enums\Animals\Breeds\ReptileBreed;
use App\Enums\Animals\Pelts\Color;
use App\Enums\Animals\Pelts\Pattern;
use App\Enums\Animals\Status;
use App\Enums\Animals\Specie as SpecieEnum;
use App\Models\Animals\AnimalStatus;
use App\Models\Animals\Breed;
use App\Models\Animals\PeltColor;
use App\Models\Animals\PeltPattern;
use App\Models\Animals\Specie;
use App\Models\User;
use Illuminate\Database\Seeder;
use Str;

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


        foreach (SpecieEnum::cases() as $animalSpecie) {
            Specie::factory([
                    'name' => $animalSpecie->value,
                ]
            )->create();
        }

        /**
         * Seeds animal breeds
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
        foreach (ReptileBreed::cases() as $breed) {
            Breed::factory([
                    'name' => $breed->value,
                    'specie_id' => 5
                ]
            )->create();
        }


        // Pelt colors
        foreach (Color::cases() as $peltColor) {
            PeltColor::factory([
                    'name' => Str::lower($peltColor->name),
                    'color' => ('#' . $peltColor->value),
                ]
            )->create();
        }

        // Pelt patterns
        foreach (Pattern::cases() as $peltPattern) {
            PeltPattern::factory([
                    'name' => $peltPattern->value
                ]
            )->create();
        }


    }
}
