<?php

namespace Database\Factories;

use App\Enums\Animals\Gender;
use App\Models\Animal;
use App\Models\AnimalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Str;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'image' => '',
            'gender' => Gender::cases()[random_int(0, 1)],
            'chip' => $this->generateUniqueChip(),
            'animal_status_id' => AnimalStatus::inRandomOrder()->value('id'),
            'personality' => $this->faker->text(),
            'born_at' => $this->faker->dateTimeBetween('-8 years'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Generates a unique chip for animals
     */
    public function generateUniqueChip(): string
    {
        $chip = Str::random(length: 16);
        if (Animal::where('chip', $chip)->exists()) {
            return $this->generateUniqueChip();
        } else {
            return $chip;
        }
    }
}
