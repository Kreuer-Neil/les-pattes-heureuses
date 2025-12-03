<?php

namespace Database\Factories;

use App\Enums\Animals\Gender;
use App\Enums\Animals\Status;
use App\Models\Animal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'image' => '',
            'gender' => Gender::cases()[random_int(0,1)],
            'chip' => $this->faker->word(),
            'animal_status' => Status::cases()[random_int(0,3)],
            /*'specie_id' => $this->faker->randomNumber(),
            'breed_id' => $this->faker->randomNumber(),
            'fur_color_id' => $this->faker->randomNumber(),
            'secondary_fur_color_id' => $this->faker->randomNumber(),
            'fur_pattern_id' => $this->faker->randomNumber(),*/
            'personality' => $this->faker->text(),
            'born_at' => $this->faker->dateTimeBetween('-8 years'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
