<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->count(1)
            ->create([
                'email' => 'admin@admin.com',
                'password' => \Hash::make('admin'),
            ]);

        $this->call(AuditoriasSeeder::class);
        $this->call(CatSiglasTipoAccionSeeder::class);
        $this->call(CatCuentaPublicaSeeder::class);
        $this->call(CatEntregaSeeder::class);
        $this->call(CatAuditoriaEspecialSeeder::class);
        $this->call(CatUaaSeeder::class);
        $this->call(CatTipoDeAuditoriaSeeder::class);
        $this->call(CatSiglasAuditoriaEspecialSeeder::class);
        $this->call(CatEnteFiscalizadoSeeder::class);
        $this->call(CatEnteDeLaAccionSeeder::class);
        $this->call(CatClaveAccionSeeder::class);
        $this->call(CatDgsegEfSeeder::class);
        $this->call(ApartadoPlantillasSeeder::class);
    }
}
