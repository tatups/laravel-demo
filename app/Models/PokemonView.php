<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PokemonView extends Model
{
    use HasFactory;


    protected $fillable = [
        'pokemon_name',
        'user_id',
    ];
}
