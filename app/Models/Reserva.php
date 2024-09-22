<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'socio_id',
        'pista_id',
        'fecha',
        'hora'
    ];
    /**
     * Get the socio that owns the Reserva
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function socio()
    {
        return $this->belongsTo(Socio::class);
    }

    /**
     * Get the pista that owns the Reserva
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pista()
    {
        return $this->belongsTo(Pista::class);
    }
}
