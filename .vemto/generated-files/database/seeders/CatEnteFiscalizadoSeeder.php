<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatEnteFiscalizado;

class CatEnteFiscalizadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatEnteFiscalizado::factory()
            ->count(5)
            ->create();
    }
}
