<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre'
    ];

    /**
     * Get all of the pistas for the Deporte
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pistas()
    {
        return $this->hasMany(Pista::class);
    }
}
