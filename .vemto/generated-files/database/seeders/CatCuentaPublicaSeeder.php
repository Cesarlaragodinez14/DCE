<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatCuentaPublica;

class CatCuentaPublicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatCuentaPublica::factory()
            ->count(5)
            ->create();
    }
}
