<?php

namespace Database\Seeders;

use App\Models\CatEntrega;
use Illuminate\Database\Seeder;

class CatEntregaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatEntrega::factory()
            ->count(5)
            ->create();
    }
}
