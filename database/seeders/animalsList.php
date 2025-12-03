<?php

use App\Enums\Animals\Breeds\BirdBreed;
use App\Enums\Animals\Breeds\CatBreed;
use App\Enums\Animals\Breeds\DogBreed;
use App\Enums\Animals\Gender;
use App\Enums\Animals\Pelts\Color;
use App\Enums\Animals\Pelts\Pattern;
use App\Enums\Animals\Specie;
use App\Enums\Animals\Status;

return [
    [
        'name' => 'Tommy',
        'image' => '',
        'gender' => Gender::Male->value,
        'chip' => 'vghftf6t7',
        'animal_status' => Status::Pending->value,
        'specie' => Specie::Cat->value, // Cat
        'breed' => CatBreed::Lion->value, // Lion
        'fur_color' => Str::lower(Color::Beige->name),
        'secondary_fur_color' => NULL,
        'fur_pattern' => NULL,
        'personality' => 'Likes cuddles and eating. Very shy.',
        'born_at' => '20/08/2003',
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
        'secondary_fur_color' => NULL,
        'fur_pattern' => NULL,
        'personality' => 'Doesn\'t eat much, very calm, stoic.',
        'born_at' => '05/04/1918',
    ],
    [
        'name' => 'Rocky',
        'image' => '',
        'gender' => Gender::Female->value,
        'chip' => 'ha9v3945',
        'animal_status' => Status::Pending->value,
        'specie' => Specie::Bird->value,
        'breed' => BirdBreed::RoyalEagle->value,
        'fur_color' => Str::lower(Color::Black->name),
        'secondary_fur_color' => Str::lower(Color::Beige->name),
        'fur_pattern' => NULL,
        'personality' => 'Solid, caring, likes hunting rodents.',
        'born_at' => '12/12/2021',
    ],
];
