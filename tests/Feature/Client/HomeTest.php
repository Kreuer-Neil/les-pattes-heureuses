<?php

use App\Models\User;

BeforeEach(function () {
    $this->user = User::factory()->create();


});

test('anyone can visit the homepage', function () {
    $this->get(route('homepage'))->assertStatus(200);

    $this->actingAs($this->user);

    $this->get(route('homepage'))->assertStatus(200);
});

//test('the number of adopted animals is right', function () {
//    $request = $this->get(route('home'));
//
//    $adoptedAnimals = Adoption::all()->count()->toString();
//
//    $request->assertSee($adoptedAnimals);
//});

