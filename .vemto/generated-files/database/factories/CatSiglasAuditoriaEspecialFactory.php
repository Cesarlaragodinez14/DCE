<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\CatSiglasAuditoriaEspecial;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatSiglasAuditoriaEspecialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatSiglasAuditoriaEspecial::class;

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
