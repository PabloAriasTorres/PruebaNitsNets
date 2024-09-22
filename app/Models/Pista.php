<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pista extends Model
{
    use HasFactory;

    protected $fillable = [
        'deporte_id',
        'longitud',
        'ancho'
    ];

    /**
     * Get the deporte that owns the Pista
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deporte()
    {
        return $this->belongsTo(Deporte::class);
    }

    /**
     * Get all of the reservas for the Pista
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Get all of the horarios for the Pista
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
