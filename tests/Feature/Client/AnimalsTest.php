<?php

use App\Enums\Animals\Breeds\DogBreed;
use App\Enums\Animals\Specie as SpecieEnum;
use App\Enums\Animals\Status;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\Specie;
use Illuminate\Support\Carbon;

function makeAvailableAnimal(array $attributes = []): Animal
{
    return Animal::factory()->create(array_merge([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ], $attributes));
}

test('anyone can access the page', function () {
    $this->get(route('client.animals'))->assertStatus(200);
});

test('without search criteria, only available animals are listed', function () {
    makeAvailableAnimal(['name' => 'Rex', 'chip' => 'chip-rex']);
    Animal::factory()->create([
        'name' => 'Ghost',
        'chip' => 'chip-ghost',
        'animal_status_id' => AnimalStatus::where('name', Status::Pending->value)->value('id'),
    ]);

    $this->get(route('client.animals'))
        ->assertSee('Rex')
        ->assertDontSee('Ghost');
});

test('the search form exposes datalists for specie, breed, color and gender', function () {
    $this->get(route('client.animals'))
        ->assertSee('id="species-options"', false)
        ->assertSee('id="breeds-options"', false)
        ->assertSee('id="colors-options"', false)
        ->assertSee('id="genders-options"', false);
});

test('searching with every criterion correct returns an exact match', function () {
    $dogSpecieId = Specie::where('name', SpecieEnum::Dog->value)->value('id');
    $corgiBreedId = Breed::where('name', DogBreed::Corgi->value)->where('specie_id', $dogSpecieId)->value('id');
    $whiteColorId = FurColor::where('name', 'white')->value('id');

    makeAvailableAnimal([
        'name' => 'Moka',
        'specie_id' => $dogSpecieId,
        'breed_id' => $corgiBreedId,
        'fur_color_id' => $whiteColorId,
        'born_at' => Carbon::now()->subYear(),
        'chip' => 'chip-moka',
    ]);

    $this->get(route('client.animals', [
        'specie' => 'chien',
        'breed' => 'corgi',
        'color' => 'blanc',
        'age' => 1,
    ]))
        ->assertStatus(200)
        ->assertSee('Moka')
        ->assertDontSee(__('client.animals.search.close_matches_title'));
});

test('one wrong criterion still surfaces the animal as a close match, alongside exact matches', function () {
    $dogSpecieId = Specie::where('name', SpecieEnum::Dog->value)->value('id');
    $corgiBreedId = Breed::where('name', DogBreed::Corgi->value)->where('specie_id', $dogSpecieId)->value('id');
    $whiteColorId = FurColor::where('name', 'white')->value('id');
    $brownColorId = FurColor::where('name', 'brown')->value('id');

    // Moka is referenced with a brown coat, but the search below (as told to Sarah by her
    // friend) asks for white — every other criterion still matches, so it should surface
    // as a "close" match rather than being excluded entirely.
    makeAvailableAnimal([
        'name' => 'Moka',
        'specie_id' => $dogSpecieId,
        'breed_id' => $corgiBreedId,
        'fur_color_id' => $brownColorId,
        'born_at' => Carbon::now()->subYear(),
        'chip' => 'chip-moka',
    ]);

    makeAvailableAnimal([
        'name' => 'Pixel',
        'specie_id' => $dogSpecieId,
        'breed_id' => $corgiBreedId,
        'fur_color_id' => $whiteColorId,
        'born_at' => Carbon::now()->subYear(),
        'chip' => 'chip-pixel',
    ]);

    $this->get(route('client.animals', [
        'specie' => 'chien',
        'breed' => 'corgi',
        'color' => 'blanc',
        'age' => 1,
    ]))
        ->assertStatus(200)
        ->assertSeeInOrder(['Pixel', __('client.animals.search.close_matches_title'), 'Moka']);
});

test('an animal matching too few criteria is excluded entirely', function () {
    $catSpecieId = Specie::where('name', SpecieEnum::Cat->value)->value('id');
    $brownColorId = FurColor::where('name', 'brown')->value('id');

    makeAvailableAnimal([
        'name' => 'Farfelu',
        'specie_id' => $catSpecieId,
        'breed_id' => null,
        'fur_color_id' => $brownColorId,
        'born_at' => Carbon::now()->subYears(10),
        'chip' => 'chip-farfelu',
    ]);

    $this->get(route('client.animals', [
        'specie' => 'chien',
        'breed' => 'corgi',
        'color' => 'blanc',
        'age' => 1,
    ]))
        ->assertStatus(200)
        ->assertDontSee('Farfelu');
});

test('a spelling mistake in a category field is still tolerated via edit-distance', function () {
    $greyColorId = FurColor::where('name', 'grey')->value('id');

    makeAvailableAnimal([
        'name' => 'Smokey',
        'fur_color_id' => $greyColorId,
        'chip' => 'chip-smokey',
    ]);

    // "geris" is a one-letter typo of "gris" (French for grey).
    $this->get(route('client.animals', ['color' => 'geris']))
        ->assertStatus(200)
        ->assertSee('Smokey');
});

test('no results shows the search-specific empty state, not the generic one', function () {
    $this->get(route('client.animals', ['q' => 'Zzyzx']))
        ->assertStatus(200)
        ->assertSee(__('client.animals.search.no_results'))
        ->assertDontSee(__('client.animals.empty'));
});
