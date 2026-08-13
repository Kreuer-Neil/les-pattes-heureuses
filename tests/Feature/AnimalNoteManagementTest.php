<?php

use App\Enums\Roles;
use App\Models\Animal;
use App\Models\AnimalNote;
use App\Models\User;
use App\Models\UserRole;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();
    $this->otherVolunteer = User::factory()->create();

    $this->animal = Animal::factory()->create();
});

test('a volunteer can add an internal note to an animal', function () {
    $this->actingAs($this->volunteer)
        ->post(route('animal-notes.store', $this->animal), [
            'title' => 'First visit',
            'text' => 'Seems very serious about adopting, has a big garden.',
        ])
        ->assertRedirect();

    expect($this->animal->notes()->count())->toBe(1);
    $note = $this->animal->notes()->first();
    expect($note->title)->toBe('First visit')
        ->and($note->text)->toBe('Seems very serious about adopting, has a big garden.')
        ->and($note->user_id)->toBe($this->volunteer->id);
});

test('an admin can add an internal note to an animal', function () {
    $this->actingAs($this->admin)
        ->post(route('animal-notes.store', $this->animal), [
            'title' => 'Checkup',
            'text' => 'All good.',
        ])
        ->assertRedirect();

    expect($this->animal->notes()->count())->toBe(1);
});

test('adding a note requires a title and text', function () {
    $this->actingAs($this->volunteer)
        ->post(route('animal-notes.store', $this->animal), [])
        ->assertSessionHasErrors(['title', 'text']);
});

test('the author can update their own note', function () {
    $note = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->volunteer->id,
        'title' => 'Original title',
        'text' => 'Original text',
    ]);

    $this->actingAs($this->volunteer)
        ->put(route('animal-notes.update', $note), [
            'title' => 'Updated title',
            'text' => 'Updated text',
        ])
        ->assertRedirect();

    expect($note->fresh()->title)->toBe('Updated title')
        ->and($note->fresh()->text)->toBe('Updated text');
});

test('a volunteer cannot update another volunteer\'s note', function () {
    $note = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->volunteer->id,
        'title' => 'Original title',
        'text' => 'Original text',
    ]);

    $this->actingAs($this->otherVolunteer)
        ->put(route('animal-notes.update', $note), [
            'title' => 'Hijacked title',
            'text' => 'Hijacked text',
        ])
        ->assertForbidden();

    expect($note->fresh()->title)->toBe('Original title');
});

test('an admin can update any note', function () {
    $note = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->volunteer->id,
        'title' => 'Original title',
        'text' => 'Original text',
    ]);

    $this->actingAs($this->admin)
        ->put(route('animal-notes.update', $note), [
            'title' => 'Admin-edited title',
            'text' => 'Admin-edited text',
        ])
        ->assertRedirect();

    expect($note->fresh()->title)->toBe('Admin-edited title');
});

test('the author can delete their own note', function () {
    $note = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->volunteer->id,
        'title' => 'Title',
        'text' => 'Text',
    ]);

    $this->actingAs($this->volunteer)
        ->delete(route('animal-notes.destroy', $note))
        ->assertRedirect();

    expect(AnimalNote::find($note->id))->toBeNull();
});

test('a volunteer cannot delete another volunteer\'s note', function () {
    $note = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->volunteer->id,
        'title' => 'Title',
        'text' => 'Text',
    ]);

    $this->actingAs($this->otherVolunteer)
        ->delete(route('animal-notes.destroy', $note))
        ->assertForbidden();

    expect(AnimalNote::find($note->id))->not->toBeNull();
});

test('an admin can delete any note', function () {
    $note = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->volunteer->id,
        'title' => 'Title',
        'text' => 'Text',
    ]);

    $this->actingAs($this->admin)
        ->delete(route('animal-notes.destroy', $note))
        ->assertRedirect();

    expect(AnimalNote::find($note->id))->toBeNull();
});

test('the animal resource includes its notes, most recent first', function () {
    $older = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->volunteer->id,
        'title' => 'Older note',
        'text' => 'Older text',
    ]);
    $older->created_at = now()->subDay();
    $older->save();

    $newer = AnimalNote::create([
        'animal_id' => $this->animal->id,
        'user_id' => $this->admin->id,
        'title' => 'Newer note',
        'text' => 'Newer text',
    ]);

    $response = $this->actingAs($this->volunteer)
        ->getJson(route('animals.show', $this->animal));

    $response->assertOk();
    $notes = $response->json('notes');

    expect($notes)->toHaveCount(2)
        ->and($notes[0]['title'])->toBe('Newer note')
        ->and($notes[0]['authorName'])->toBe($this->admin->name)
        ->and($notes[1]['title'])->toBe('Older note');
});
