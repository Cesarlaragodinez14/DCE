<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\CatTipoDeAuditoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatTipoDeAuditoriaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatTipoDeAuditoria::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'valor' => fake()->word(),
            'descripcion' => fake()->word(),
            'activo' => fake()->boolean(),
        ];
    }
}
