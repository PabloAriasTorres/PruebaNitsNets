<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'reservada'
    ];

    /**
     * Get the pista that owns the Horario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pista()
    {
        return $this->belongsTo(Pista::class);
    }
}
