<?php

namespace App\Services;

use App\Enums\PendingApprobationStatus;
use App\Models\AdoptionRequest;
use App\Models\ContactMessage;
use App\Models\PendingAnimalChanges;
use Illuminate\Support\Collection;

/**
 * Admin-only: what still needs an admin's action, and a heads-up on unread contact messages.
 *
 * Adoption requests and animal change proposals are queried directly rather than tracked
 * through a separate notifications table — both already carry their own status and
 * created_at, and they self-clear from this feed the moment their status changes (no
 * separate read/resolved bookkeeping to keep in sync). Contact messages don't have an
 * admin-action workflow of their own (that could change later, e.g. trusted volunteers
 * handling them), so they're summarized as a plain unread count instead of appearing here
 * item-by-item.
 */
class AdminAttentionFeed
{
    /**
     * Adoption requests and animal changes still awaiting action, newest first. Returns the
     * raw models (mixed types) — formatting for the frontend is AdminAttentionCollection's job.
     *
     * @return Collection<int, AdoptionRequest|PendingAnimalChanges>
     */
    public static function items(): Collection
    {
        $adoptionRequests = AdoptionRequest::with(['animal', 'adopterProfile'])
            ->whereIn('status', [PendingApprobationStatus::Unattended, PendingApprobationStatus::Pending])
            ->get();

        $animalChanges = PendingAnimalChanges::with(['animal', 'user'])
            ->where('status', PendingApprobationStatus::Pending)
            ->get();

        return $adoptionRequests->concat($animalChanges)
            ->sortByDesc('created_at')
            ->values();
    }

    public static function unreadMessageCount(): int
    {
        return ContactMessage::whereNull('read_at')->count();
    }
}
