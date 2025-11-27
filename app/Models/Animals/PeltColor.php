<?php

namespace App\Models\Animals;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeltColor extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'color'];
}
