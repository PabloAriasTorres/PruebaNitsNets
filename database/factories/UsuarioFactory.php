<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = $this->faker->name();
        $email = $this->faker->email();
        $contrasenya = $this->faker->password();

        return [
            //
            'nombre' => $nombre,
            'email' => $email,
            'contrasenya' => bcrypt($contrasenya) //HASHEAR LA CONTRASEÑA POR SEGURIDAD
        ];
    }
}
