<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Breed extends Model
{
    protected $fillable = ['name'];

    protected function label(): Attribute
    {
        return Attribute::get(fn () => Str::headline($this->name));
    }
}
