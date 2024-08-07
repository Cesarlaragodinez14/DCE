<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatTipoDeAuditoria;

class CatTipoDeAuditoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatTipoDeAuditoria::factory()
            ->count(5)
            ->create();
    }
}
