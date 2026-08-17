<?php

namespace App\Policies;

use App\Models\AnimalNote;
use App\Models\User;

class AnimalNotePolicy
{
    public function create(User $user): bool
    {
        return $user->exists();
    }

    public function update(User $user, AnimalNote $animalNote): bool
    {
        return $user->isAdmin() || $user->id === $animalNote->user_id;
    }

    public function delete(User $user, AnimalNote $animalNote): bool
    {
        return $this->update($user, $animalNote);
    }
}
