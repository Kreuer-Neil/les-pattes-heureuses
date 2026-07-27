<?php

use App\Enums\Animals\Status;
use App\Models\Animal;
use App\Models\AnimalStatus;
use Database\Seeders\AnimalOptionsSeeder;

beforeEach(function () {
    $this->seed(AnimalOptionsSeeder::class);

    $this->animal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ]);
});

test('the animal page shows a share button', function () {
    $this->get(route('client.animal.show', $this->animal))
        ->assertStatus(200)
        ->assertSee(__('client.animal.share.button'))
        ->assertSee('share-dialog', false);
});

test('the share dialog contains the animal\'s canonical url', function () {
    $this->get(route('client.animal.show', $this->animal))
        ->assertSee(route('client.animal.show', $this->animal), false);
});
