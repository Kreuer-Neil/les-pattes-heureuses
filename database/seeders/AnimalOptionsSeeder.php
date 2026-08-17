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
use App\Enums\Animals\Status;
use App\Models\AnimalStatus;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Models\Specie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnimalOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SpecieEnum::cases() as $animalSpecie) {
            Specie::firstOrCreate([
                    'name' => $animalSpecie->value
                ]
            );
        }

        // Adding a case to the Status enum and re-running this seeder is enough to make it available —
        // no migration needed, since animals.animal_status_id just points at this table's rows.
        foreach (Status::cases() as $status) {
            AnimalStatus::firstOrCreate([
                    'name' => $status->value
                ]
            );
        }

        /**
         * Seeds animal breeds
         */
        // Dogs
        foreach (DogBreed::cases() as $breed) {
            Breed::firstOrCreate([
                    'name' => $breed->value,
                    'specie_id' => 1
                ]
            );
        }

        // Cats
        foreach (CatBreed::cases() as $breed) {
            Breed::firstOrCreate([
                    'name' => $breed->value,
                    'specie_id' => 2
                ]
            );
        }

        // Birds
        foreach (BirdBreed::cases() as $breed) {
            Breed::firstOrCreate([
                    'name' => $breed->value,
                    'specie_id' => 3
                ]
            );
        }

        // Horses
        foreach (HorseBreed::cases() as $breed) {
            Breed::firstOrCreate([
                    'name' => $breed->value,
                    'specie_id' => 4
                ]
            );
        }

        // Reptiles
        foreach (ReptileBreed::cases() as $breed) {
            Breed::firstOrCreate([
                    'name' => $breed->value,
                    'specie_id' => 5
                ]
            );
        }


        // Pelt colors
        foreach (Color::cases() as $peltColor) {
            FurColor::firstOrCreate([
                    'name' => Str::lower($peltColor->name),
                    'color' => ('#' . $peltColor->value),
                ]
            );
        }

        // Pelt patterns
        foreach (Pattern::cases() as $peltPattern) {
            FurPattern::firstOrCreate([
                    'name' => $peltPattern->value
                ]
            );
        }
    }
}
