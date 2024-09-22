<?php

namespace Database\Seeders;

use App\Models\Socio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Socio::factory()
            ->count(3)
            ->create();
    }
}
