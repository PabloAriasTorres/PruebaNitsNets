<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Deporte;
use App\Models\Horario;
use App\Models\Pista;
use DateTime;
use DateInterval;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pista>
 */
class PistaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $longitud = $this->faker->randomFloat(1, 20, 35);
        $ancho = $this->faker->randomFloat(1, 15, 25);
        return [
            'deporte_id' => Deporte::factory(),
            'longitud' => $longitud,
            'ancho' => $ancho
        ];
    }

    //ES UNA FUNCIÓN QUE GENERA LOS HORARIOS DE 8:00 A 22:00 DE TODOS LOS DÍAS DE OCTUBRE
    public function configure()
    {
        return $this->afterCreating(function (Pista $pista) {
            $horarios = [];
            $primerDia = new DateTime('2024-10-01');
            $ultimoDia = new DateTime('2024-10-31');

            while ($primerDia <= $ultimoDia) {
                $hora = new DateTime('08:00:00');

                while ($hora <= new DateTime('22:00:00')) {
                    $horarios[] = [
                        'pista_id' => $pista->id,
                        'fecha' => $primerDia->format('Y-m-d'),
                        'hora' => $hora->format('H:i:s'),
                        'reservada' => false,
                    ];

                    //SE SUMA UNA HORA
                    $hora->add(new DateInterval('PT1H'));
                }

                //SE SUMA UN DÍA
                $primerDia->add(new DateInterval('P1D'));
            }

            Horario::insert($horarios);
        });
    }
}
