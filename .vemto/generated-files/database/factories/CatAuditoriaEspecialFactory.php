<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\CatAuditoriaEspecial;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatAuditoriaEspecialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatAuditoriaEspecial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'valor' => fake()->word(),
            'descripcion' => fake()->sentence(15),
            'activo' => fake()->boolean(),
        ];
    }
}
