<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatSiglasTipoAccion;

class CatSiglasTipoAccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatSiglasTipoAccion::factory()
            ->count(5)
            ->create();
    }
}
