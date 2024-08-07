<?php

namespace Database\Seeders;

use App\Models\CatClaveAccion;
use Illuminate\Database\Seeder;

class CatClaveAccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatClaveAccion::factory()
            ->count(5)
            ->create();
    }
}
