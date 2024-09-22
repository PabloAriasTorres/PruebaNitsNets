<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deporte>
 */
class DeporteFactory extends Factory
{
    protected static $deportes = ['Baloncesto', 'Fútbol', 'Tenis', 'Paddle'];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //Se guarda el primer valor del array y luego ese mismo valor se borra del array para que no pueda volver a salir
        $nombre = array_shift(static::$deportes);
        return [
            //
            'nombre' => $nombre
        ];
    }
}
