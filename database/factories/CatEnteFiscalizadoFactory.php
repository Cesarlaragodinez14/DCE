<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\CatEnteFiscalizado;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatEnteFiscalizadoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatEnteFiscalizado::class;

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
