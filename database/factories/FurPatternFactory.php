<?php

namespace Database\Factories;

use App\Models\FurPattern;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class FurPatternFactory extends Factory
{
    protected $model = FurPattern::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
