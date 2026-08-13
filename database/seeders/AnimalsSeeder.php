<?php

namespace Database\Seeders;

use App\Enums\Animals\Breeds\BirdBreed;
use App\Enums\Animals\Breeds\CatBreed;
use App\Enums\Animals\Breeds\DogBreed;
use App\Enums\Animals\Breeds\HorseBreed;
use App\Enums\Animals\Gender;
use App\Enums\Animals\MovementType;
use App\Enums\Animals\Pelts\Color;
use App\Enums\Animals\Pelts\Pattern;
use App\Enums\Animals\Specie;
use App\Enums\Animals\Status;
use App\Enums\Animals\Vaccines;
use App\Models\Animal;
use App\Models\AnimalMovement;
use App\Models\AnimalNote;
use App\Models\AnimalStatus;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Models\Specie as SpecieModel;
use App\Models\User;
use App\Models\Vaccine;
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
        // Volunteers/notes authors — both already exist by this point (UsersSeeder/VolunteersSeeder run first).
        $elise = User::where('email', 'test@example.com')->first();
        $thomas = User::where('email', 'thomas@les-pattes-heureuses.test')->first();

        $pets = [
            [
                'name' => 'Tommy',
                'image' => 'tommy',
                'gender' => Gender::Male,
                'specie' => Specie::Cat,
                'breed' => CatBreed::Lion,
                'fur_color' => Color::Beige,
                'personality' => 'Aime les câlins et manger. Très timide. Mais tous les chats qui partagent sa cage disparaissent mystérieusement…',
                'born_at' => '20/08/2003',
                'recovered_at' => '15/03/2024',
                'vaccines' => [
                    ['vaccine' => Vaccines::Rabies, 'date' => '15/03/2024'],
                    ['vaccine' => Vaccines::FelineViralRhinotracheitis, 'date' => '15/03/2024'],
                ],
                'notes' => [
                    [
                        'author' => $thomas,
                        'title' => 'Comportement avec les autres chats',
                        'text' => "Tommy reste très craintif en présence d'autres chats. À surveiller de près lors des sorties en extérieur communes.",
                    ],
                ],
            ],
            [
                'name' => 'Schrödinger',
                'image' => 'schrodinger',
                'gender' => Gender::Female,
                'specie' => Specie::Cat,
                'breed' => CatBreed::EuropeanShorthair,
                'fur_color' => Color::Black,
                'secondary_fur_color' => Color::White,
                'fur_pattern' => Pattern::Tabby,
                'personality' => 'Difficile de dire si elle va bien ou même si elle est vivante.',
                'born_at' => '07/04/2019',
                'recovered_at' => '01/11/2025',
                'vaccines' => [
                    ['vaccine' => Vaccines::FelineCalicivirus, 'date' => '01/11/2025'],
                ],
                'notes' => [
                    [
                        'author' => $elise,
                        'title' => 'Suivi vétérinaire',
                        'text' => 'Difficile à évaluer au premier abord : demander à un bénévole de repasser la voir plusieurs fois dans la journée avant de conclure quoi que ce soit.',
                    ],
                ],
            ],
            [
                'name' => 'Papy',
                'image' => 'papy',
                'gender' => Gender::Male,
                'animal_status' => Status::Healing,
                'specie' => Specie::Dog,
                'breed' => DogBreed::GermanShepherd,
                'fur_color' => Color::Black,
                'personality' => 'Ne mange pas beaucoup, très calme, stoïque.',
                'born_at' => '05/04/1918',
                'recovered_at' => '20/06/2026',
                'vaccines' => [
                    ['vaccine' => Vaccines::Distemper, 'date' => '20/06/2026'],
                    ['vaccine' => Vaccines::CanineParvovirus, 'date' => '20/06/2026'],
                ],
                'notes' => [
                    [
                        'author' => $thomas,
                        'title' => 'État de santé',
                        'text' => 'Papy mange peu depuis son arrivée en soins. Le vétérinaire a été prévenu, réévaluation prévue la semaine prochaine.',
                    ],
                ],
            ],
            [
                'name' => 'Rocky',
                'image' => 'rocky',
                'gender' => Gender::Female,
                'specie' => Specie::Bird,
                'breed' => BirdBreed::RoyalEagle,
                'fur_color' => Color::Black,
                'secondary_fur_color' => Color::Beige,
                'personality' => 'Solide, protecteur, aime chasser les rongeurs.',
                'born_at' => '12/12/2021',
                'recovered_at' => '25/07/2026',
                'vaccines' => [
                    ['vaccine' => Vaccines::AvianPolyomavirus, 'date' => '25/07/2026'],
                ],
            ],

            [
                'name' => 'Ponyta',
                'image' => 'ponyta',
                'gender' => Gender::Female,
                'specie' => Specie::Horse,
                'breed' => HorseBreed::Pony,
                'personality' => 'Aime les balades.',
                'recovered_at' => '10/01/2026',
                'vaccines' => [
                    ['vaccine' => Vaccines::Tetanus, 'date' => '10/01/2026'],
                    ['vaccine' => Vaccines::EquineInfluenza, 'date' => '10/01/2026'],
                ],
                'notes' => [
                    [
                        'author' => $thomas,
                        'title' => 'Adaptation au box',
                        'text' => "Ponyta s'adapte bien au box, elle apprécie particulièrement les sorties au paddock le matin.",
                    ],
                ],
            ],
            [
                'name' => 'Dexter',
                'image' => 'dexter',
                'gender' => Gender::Male,
                'specie' => Specie::Cat,
                'breed' => CatBreed::AmericanShorthair,
                'fur_color' => Color::Black,
                'secondary_fur_color' => Color::Beige,
                'personality' => 'A une fâcheuse tendance à virer au bleu.',
                'recovered_at' => '22/09/2025',
                'vaccines' => [
                    ['vaccine' => Vaccines::FelinePanleukopenia, 'date' => '22/09/2025'],
                ],
                'notes' => [
                    [
                        'author' => $elise,
                        'title' => 'Coloration inhabituelle',
                        'text' => 'Dexter a de nouveau viré au bleu ce matin — contacter le vétérinaire si cela persiste au-delà de 48h.',
                    ],
                ],
            ],
            [
                'name' => 'Barbe Rousse',
                'image' => 'barbe_rousse',
                'gender' => Gender::Male,
                'specie' => Specie::Cat,
                'breed' => CatBreed::EuropeanShorthair,
                'fur_color' => Color::Orange,
                'personality' => 'Semble parfois menaçant, surtout avec son trésor : toutes les pelotes de laine qu\'il peut trouver.',
                'recovered_at' => '03/03/2025',
                'notes' => [
                    [
                        'author' => $thomas,
                        'title' => 'Collection de pelotes de laine',
                        'text' => "Récupère toutes les pelotes de laine qu'il trouve dans son enclos et semble en faire une collection dans un coin — inutile d'essayer de les lui reprendre.",
                    ],
                ],
            ],
            [
                'name' => 'Nymeria',
                'image' => 'nymeria',
                'animal_status' => Status::Deceased,
                'gender' => Gender::Female,
                'specie' => Specie::Dog,
                'breed' => DogBreed::GermanShepherd,
                'fur_color' => Color::Black,
                'secondary_fur_color' => Color::Beige,
                'fur_pattern' => Pattern::Tabby,
                'personality' => 'Douce, calme, très affectueuse.',
                'born_at' => '02/08/2014',
                'recovered_at' => '05/02/2025',
                'left_at' => '12/12/2025',
                'vaccines' => [
                    ['vaccine' => Vaccines::Rabies, 'date' => '05/02/2025'],
                    ['vaccine' => Vaccines::Distemper, 'date' => '05/02/2025'],
                ],
            ],
        ];

        foreach ($pets as $pet) {
            $status = $pet['animal_status'] ?? Status::Available;

            $animal = Animal::factory([
                'name' => $pet['name'],
                'gender' => $pet['gender']->value,
                'animal_status_id' => AnimalStatus::where('name', $status->value)->first()->id,
                'specie_id' => SpecieModel::where('name', $pet['specie']->value)->first()->id,
                'breed_id' => Breed::where('name', $pet['breed']->value)->first()->id,
                'fur_color_id' => isset($pet['fur_color'])
                    ? FurColor::where('name', Str::lower($pet['fur_color']->name))->first()->id
                    : null,
                'secondary_fur_color_id' => isset($pet['secondary_fur_color'])
                    ? FurColor::where('name', Str::lower($pet['secondary_fur_color']->name))->first()->id
                    : null,
                'fur_pattern_id' => isset($pet['fur_pattern'])
                    ? FurPattern::where('name', $pet['fur_pattern']->value)->first()->id
                    : null,
                'personality' => $pet['personality'],
                ...(isset($pet['born_at'])
                    ? ['born_at' => Carbon::createFromFormat('d/m/Y', $pet['born_at'])->format('Y-m-d')]
                    : []),
            ])->make();

            $vaccines = collect($pet['vaccines'] ?? [])->map(fn (array $vaccine) => [
                'vaccine_id' => Vaccine::where('name', $vaccine['vaccine']->value)->first()->id,
                'vaccinated_at' => Carbon::createFromFormat('d/m/Y', $vaccine['date'])->format('Y-m-d'),
            ])->all();

            AnimalWriter::create(
                $animal,
                $vaccines,
                isset($pet['recovered_at'])
                    ? Carbon::createFromFormat('d/m/Y', $pet['recovered_at'])->format('Y-m-d')
                    : null,
            );

            foreach ($pet['notes'] ?? [] as $note) {
                AnimalNote::create([
                    'animal_id' => $animal->id,
                    'user_id' => $note['author']->id,
                    'title' => $note['title'],
                    'text' => $note['text'],
                ]);
            }

            if (isset($pet['left_at'])) {
                $movementType = match ($status) {
                    Status::Deceased => MovementType::DeceasedDeparture,
                    Status::Adopted => MovementType::AdoptedDeparture,
                    default => null,
                };

                if ($movementType !== null) {
                    AnimalMovement::create([
                        'animal_id' => $animal->id,
                        'type' => $movementType,
                        'occurred_at' => Carbon::createFromFormat('d/m/Y', $pet['left_at'])->format('Y-m-d'),
                    ]);
                }
            }
        }
    }
}
