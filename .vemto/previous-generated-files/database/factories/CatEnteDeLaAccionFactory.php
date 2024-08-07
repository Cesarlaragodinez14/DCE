<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\CatEnteDeLaAccion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatEnteDeLaAccionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatEnteDeLaAccion::class;

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
            'activo' => fake()->word(),
        ];
    }
}
