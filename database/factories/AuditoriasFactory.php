<?php

namespace Database\Factories;

use App\Models\Auditorias;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditoriasFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Auditorias::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clave_de_accion' => fake()->word(),
            'titulo' => fake()->word(),
            'numero_de_auditoria' => fake()->word(),
            'nombre_director_general' => fake()->word(),
            'direccion_de_area' => fake()->word(),
            'nombre_director_de_area' => fake()->word(),
            'sub_direccion_de_area' => fake()->word(),
            'nombre_sub_director_de_area' => fake()->word(),
            'jefe_de_departamento' => fake()->word(),
            'entrega' => \App\Models\CatEntrega::factory(),
            'auditoria_especial' => \App\Models\CatAuditoriaEspecial::factory(),
            'siglas_dg_uaa' => \App\Models\CatUaa::factory(),
            'tipo_de_auditoria' => \App\Models\CatTipoDeAuditoria::factory(),
            'siglas_auditoria_especial' => \App\Models\CatSiglasAuditoriaEspecial::factory(),
            'ente_fiscalizado' => \App\Models\CatEnteFiscalizado::factory(),
            'ente_de_la_accion' => \App\Models\CatEnteDeLaAccion::factory(),
            'clave_accion' => \App\Models\CatClaveAccion::factory(),
            'siglas_tipo_accion' => \App\Models\CatSiglasTipoAccion::factory(),
            'dgseg_ef' => \App\Models\CatDgsegEf::factory(),
            'cuenta_publica' => \App\Models\CatCuentaPublica::factory(),
        ];
    }
}
