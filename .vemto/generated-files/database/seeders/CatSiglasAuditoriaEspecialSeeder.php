<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatSiglasAuditoriaEspecial;

class CatSiglasAuditoriaEspecialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatSiglasAuditoriaEspecial::factory()
            ->count(5)
            ->create();
    }
}
