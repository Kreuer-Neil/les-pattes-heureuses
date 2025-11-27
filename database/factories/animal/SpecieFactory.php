<?php

namespace Database\Factories\animal;

use App\Models\Animal\Specie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SpecieFactory extends Factory
{
    protected $model = Specie::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
