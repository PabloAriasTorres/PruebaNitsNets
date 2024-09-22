<?php

namespace Database\Seeders;

use App\Models\Deporte;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Deporte::factory()
            ->count(2)
            ->hasPistas(4)
            ->create();
        Deporte::factory()
            ->count(2)
            ->hasPistas(3)
            ->create();
    }
}
