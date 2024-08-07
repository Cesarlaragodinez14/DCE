<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatEnteDeLaAccion;

class CatEnteDeLaAccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatEnteDeLaAccion::factory()
            ->count(5)
            ->create();
    }
}
