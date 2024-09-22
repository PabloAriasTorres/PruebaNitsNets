<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Socio>
 */
class SocioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = $this->faker->name();
        $dni = $this->generarDni();
        return [
            //
            'nombre' => $nombre,
            'dni' => $dni
        ];
    }

    //UNA FUNCIÓN QUE SE ASEGURA DE GENERAR ÚNICAMENTE LOS DNI QUE EXISTAN
    public function generarDni(): string
    {
        $dniNumeros = "";
        for($i=0; $i<8; $i++){
            $dniNumeros .= $this->faker->numberBetween(0,9);
        }
        $dniPosiblesLetras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $dniLetra = $dniPosiblesLetras[((int)$dniNumeros) % 23];

        return $dniNumeros . $dniLetra;
    }
}
