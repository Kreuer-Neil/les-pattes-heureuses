<?php

namespace App\Models;

use App\Enums\Animals\Status;
use App\Enums\PendingApprobationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'gender', 'chip', 'personality', 'born_at',
        'breed_id', 'fur_color_id', 'fur_pattern_id', 'secondary_fur_color_id', 'specie_id', 'animal_status_id',
    ];

    // Non-admins can change animals :
    // - status (other than adopted/deceased)
    // - Image
    // - Add notes

    protected $casts = [
        'born_at' => 'date',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(AnimalStatus::class, 'animal_status_id');
    }

    public function specie(): BelongsTo
    {
        return $this->belongsTo(Specie::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function furColor(): BelongsTo
    {
        return $this->belongsTo(FurColor::class);
    }

    public function secondaryFurColor(): BelongsTo
    {
        return $this->belongsTo(FurColor::class, 'secondary_fur_color_id');
    }

    public function furSchema(): BelongsTo
    {
        return $this->belongsTo(FurPattern::class);
    }

    // More specific fn
    public function notes(): HasMany
    {
        return $this->hasMany(AnimalNote::class);
    }

    public function vaccines(): BelongsToMany
    {
        return $this->belongsToMany(Vaccine::class, AnimalVaccine::class)->withPivot('id', 'vaccinated_at');
    }

    public function adoptionRequests(): HasMany
    {
        return $this->hasMany(AdoptionRequest::class);
    }

    public function availableIfUnrequested(): void
    {
        if ($this->status->name !== Status::Pending->value) {
            return;
        }

        $hasActiveRequest = $this->adoptionRequests()
            ->whereIn('status', [PendingApprobationStatus::Unattended->value, PendingApprobationStatus::Pending->value])
            ->exists();

        if ($hasActiveRequest) {
            return;
        }

        $this->animal_status_id = AnimalStatus::where('name', Status::Available->value)->value('id');
        $this->save();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(AnimalMovement::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('status', fn (Builder $status) => $status->where('name', Status::Available->value));
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereHas('status', fn (Builder $status) => $status->whereIn('name', [
            Status::Available->value,
            Status::Healing->value,
            // Status::Pending->value,
        ]));
    }

    // Deceased animals play the role a soft-delete would elsewhere: the row and its
    // history stay, but it shouldn't clutter general admin listings by default. This
    // is opt-in (not a global scope) so relations like AdoptionRequest::animal() never
    // silently resolve to null once the animal they point to is marked deceased.
    public function scopeExcludingDeceased(Builder $query): Builder
    {
        return $query->whereHas('status', fn (Builder $status) => $status->where('name', '!=', Status::Deceased->value));
    }
}
