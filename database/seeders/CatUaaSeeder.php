<?php

namespace Database\Seeders;

use App\Models\CatUaa;
use Illuminate\Database\Seeder;

class CatUaaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatUaa::factory()
            ->count(5)
            ->create();
    }
}
