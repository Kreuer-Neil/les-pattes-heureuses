<?php

namespace Database\Seeders;

use App\Enums\ContactMessageType;
use App\Enums\Roles;
use App\Models\ContactMessage;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

// README personas seeder.
// TODO edit when volunteers creation is set if needed.
class VolunteersSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'thomas@les-pattes-heureuses.test'],
            [
                'name' => 'Thomas',
                'password' => 'password',
                'email_verified_at' => now(),
                'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Volunteer->value])->id,
            ]
        );

        ContactMessage::firstOrCreate(
            ['email' => 'chloe@example.com'],
            [
                'first_name' => 'Chloé',
                'last_name' => 'Bénévole',
                'type' => ContactMessageType::VolunteerRequest,
                'content' => 'Bonjour, je suis étudiante et je souhaiterais devenir bénévole au refuge. Je suis disponible plusieurs soirs par semaine et le week-end. Au plaisir de vous rencontrer !',
            ]
        );
    }
}
