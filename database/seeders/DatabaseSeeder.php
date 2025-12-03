<?php

namespace Database\Seeders;

use App\Enums\Animals\Breeds\BirdBreed;
use App\Enums\Animals\Breeds\CatBreed;
use App\Enums\Animals\Breeds\DogBreed;
use App\Enums\Animals\Breeds\HorseBreed;
use App\Enums\Animals\Breeds\ReptileBreed;
use App\Enums\Animals\Pelts\Color;
use App\Enums\Animals\Pelts\Pattern;
use App\Enums\Animals\Specie as SpecieEnum;
use App\Models\Animal;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Models\Specie;
use App\Models\User;
use Carbon\Carbon;
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

        foreach (SpecieEnum::cases() as $animalSpecie) {
            Specie::factory([
                    'name' => $animalSpecie->value
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
            FurColor::factory([
                    'name' => Str::lower($peltColor->name),
                    'color' => ('#' . $peltColor->value),
                ]
            )->create();
        }

        // Pelt patterns
        foreach (Pattern::cases() as $peltPattern) {
            FurPattern::factory([
                    'name' => $peltPattern->value
                ]
            )->create();
        }

        $pets = require 'animalsList.php';
        foreach ($pets as $pet) {
            Animal::factory([
                'name' => $pet['name'],
                'gender' => $pet['gender'],
                'chip' => $pet['chip'],
                'animal_status' => $pet['animal_status'],
                'specie_id' => Specie::where('name', $pet['specie'])->first()->id,
                'breed_id' => Breed::where('name', $pet['breed'])->first()->id,
                'fur_color_id' => ($pet['fur_color'] ? FurColor::where('name', $pet['fur_color'])->first()->id : NULL),
                'secondary_fur_color_id' => ($pet['secondary_fur_color'] ? FurColor::where('name', $pet['secondary_fur_color'])->first()->id : NULL),
                'fur_pattern_id' => ($pet['fur_pattern'] ? FurPattern::where('name', $pet['fur_pattern'])->first()->id : NULL),
                'personality' => $pet['personality'],
                'born_at' => Carbon::createFromFormat('d/m/Y',$pet['born_at'])->format('Y-m-d'),
            ])
            ->create();
        }


    }
}
