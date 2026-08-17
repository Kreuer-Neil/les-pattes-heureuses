<?php

namespace Database\Seeders;

use App\Enums\Animals\Vaccines;
use App\Models\Vaccine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VaccinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Vaccines::cases() as $vaccine) {
            Vaccine::firstOrCreate([
                    'name' => $vaccine->value,
                ]
            );
        }
    }
}
