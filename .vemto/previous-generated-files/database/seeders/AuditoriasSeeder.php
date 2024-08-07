<?php

namespace Database\Seeders;

use App\Models\Auditorias;
use Illuminate\Database\Seeder;

class AuditoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Auditorias::factory()
            ->count(5)
            ->create();
    }
}
