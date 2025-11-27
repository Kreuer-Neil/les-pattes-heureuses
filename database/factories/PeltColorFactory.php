<?php

namespace Database\Factories;

use App\Models\PeltColor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PeltColorFactory extends Factory
{
    protected $model = PeltColor::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'hex' => $this->faker->hexColor(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
