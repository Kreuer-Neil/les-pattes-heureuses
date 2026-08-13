<?php

namespace Database\Seeders;

use App\Enums\PendingApprobationStatus;
use App\Models\AdopterProfile;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use Illuminate\Database\Seeder;

// README scenario 1 adoption request as a seeder
class AdoptionRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $adopterProfile = AdopterProfile::firstOrCreate(
            ['email' => 'sarah@example.com'],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Dupont',
            ]
        );

        $animal = Animal::where('name', 'Tommy')->firstOrFail();

        AdoptionRequest::firstOrCreate(
            ['animal_id' => $animal->id, 'adopter_profile_id' => $adopterProfile->id],
            [
                'content' => "Bonjour, je viens de découvrir la fiche de {$animal->name} et j'aimerais beaucoup planifier une rencontre pour faire sa connaissance. Je suis disponible en soirée et le week-end. Merci d'avance !",
                'status' => PendingApprobationStatus::Unattended,
            ]
        );
    }
}
