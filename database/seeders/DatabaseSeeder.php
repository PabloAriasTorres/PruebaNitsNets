<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Deporte;
use App\Models\Socio;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsuarioSeeder::class,
            DeporteSeeder::class,
            SocioSeeder::class
        ]);
    }
}
