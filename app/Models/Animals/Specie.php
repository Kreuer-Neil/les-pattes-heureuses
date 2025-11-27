<?php

namespace App\Models\Animals;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specie extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function breeds():BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }
}
