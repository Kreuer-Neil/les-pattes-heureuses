<?php

namespace Database\Factories\animal;

use App\Models\Animals\Breed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreedFactory extends Factory
{
    protected $model = Breed::class;

    public function definition(): array
    {
        return [

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
