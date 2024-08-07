<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatAuditoriaEspecial;

class CatAuditoriaEspecialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatAuditoriaEspecial::factory()
            ->count(5)
            ->create();
    }
}
