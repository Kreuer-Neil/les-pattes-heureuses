<?php

use App\Models\Animal;
use App\Models\User;

BeforeEach(function () {
    $this->user = User::factory()->create();


});

test('anyone can visit the homepage', function () {
    $this->get(route('homepage'))->assertStatus(200);

    $this->actingAs($this->user);

    $this->get(route('homepage'))->assertStatus(200);
});

test('the number of found animals is right', function () {
    $request = $this->get(route('homepage'));

    $adoptedAnimals = Animal::all()->count();

    $request->assertSee($adoptedAnimals);
});

/*test('users with JS can switch to the volunteer form', function () {
});
test('users can send a message through the contact form', function () {
});*/
