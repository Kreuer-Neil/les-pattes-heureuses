<?php

namespace Database\Factories;

use App\Models\AnimalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AnimalStatusFactory extends Factory
{
    protected $model = AnimalStatus::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
