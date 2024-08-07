<?php

namespace Database\Seeders;

use App\Models\CatDgsegEf;
use Illuminate\Database\Seeder;

class CatDgsegEfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CatDgsegEf::factory()
            ->count(5)
            ->create();
    }
}
