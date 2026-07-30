<?php

namespace Database\Seeders;

use App\Enums\Animals\Breeds\BirdBreed;
use App\Enums\Animals\Breeds\CatBreed;
use App\Enums\Animals\Breeds\DogBreed;
use App\Enums\Animals\Gender;
use App\Enums\Animals\Pelts\Color;
use App\Enums\Animals\Pelts\Pattern;
use App\Enums\Animals\Specie;
use App\Enums\Animals\Status;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Services\AnimalWriter;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Str;

class AnimalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO add all pets + put images and seed with images (add them to Git too)
        // TODO add pet with adoptionRequest
        $pets = [
            [
                'name' => 'Tommy',
                'image' => '',
                'gender' => Gender::Male->value,
                'chip' => 'vghftf6t7',
                'animal_status' => Status::Available->value,
                'specie' => Specie::Cat->value, // Cat
                'breed' => CatBreed::Lion->value, // Lion
                'fur_color' => Str::lower(Color::Beige->name),
                'secondary_fur_color' => null,
                'fur_pattern' => null,
                'personality' => 'Likes cuddles and eating. Very shy.',
                'born_at' => '20/08/2003',
                'recovered_at' => '15/03/2024',
            ],
            [
                'name' => 'Schrödinger',
                'image' => '',
                'gender' => Gender::Female,
                'chip' => 'e45r6t7ziu',
                'animal_status' => Status::Available->value,
                'specie' => Specie::Cat->value,
                'breed' => CatBreed::EuropeanShorthair->value,
                'fur_color' => Str::lower(Color::Black->name),
                'secondary_fur_color' => Str::lower(Color::White->name),
                'fur_pattern' => Pattern::Tabby,
                'personality' => 'Hard to tell if she\'s fine and alive.',
                'born_at' => '07/04/2019',
                'recovered_at' => '01/11/2025',
            ],
            [
                'name' => 'Papy',
                'image' => '',
                'gender' => Gender::Male->value,
                'chip' => '57djd09hf',
                'animal_status' => Status::Healing->value,
                'specie' => Specie::Dog->value,
                'breed' => DogBreed::GermanShepherd->value,
                'fur_color' => Str::lower(Color::Black->name),
                'secondary_fur_color' => null,
                'fur_pattern' => null,
                'personality' => 'Doesn\'t eat much, very calm, stoic.',
                'born_at' => '05/04/1918',
                'recovered_at' => '20/06/2026',
            ],
            [
                'name' => 'Rocky',
                'image' => '',
                'gender' => Gender::Female->value,
                'chip' => 'ha9v3945',
                'animal_status' => Status::Available->value,
                'specie' => Specie::Bird->value,
                'breed' => BirdBreed::RoyalEagle->value,
                'fur_color' => Str::lower(Color::Black->name),
                'secondary_fur_color' => Str::lower(Color::Beige->name),
                'fur_pattern' => null,
                'personality' => 'Solid, caring, likes hunting rodents.',
                'born_at' => '12/12/2021',
                'recovered_at' => '25/07/2026',
            ],
        ];

        foreach ($pets as $pet) {
            $animal = Animal::factory([
                'name' => $pet['name'],
                'gender' => $pet['gender'],
                'chip' => $pet['chip'],
                'animal_status_id' => AnimalStatus::where('name', $pet['animal_status'])->first()->id,
                'specie_id' => \App\Models\Specie::where('name', $pet['specie'])->first()->id,
                'breed_id' => Breed::where('name', $pet['breed'])->first()->id,
                'fur_color_id' => ($pet['fur_color'] ? FurColor::where('name', $pet['fur_color'])->first()->id : null),
                'secondary_fur_color_id' => ($pet['secondary_fur_color'] ? FurColor::where('name', $pet['secondary_fur_color'])->first()->id : null),
                'fur_pattern_id' => ($pet['fur_pattern'] ? FurPattern::where('name', $pet['fur_pattern'])->first()->id : null),
                'personality' => $pet['personality'],
                'born_at' => Carbon::createFromFormat('d/m/Y', $pet['born_at'])->format('Y-m-d'),
            ])->make();

            AnimalWriter::create(
                $animal,
                [],
                Carbon::createFromFormat('d/m/Y', $pet['recovered_at'])->format('Y-m-d'),
            );
        }
    }
}
