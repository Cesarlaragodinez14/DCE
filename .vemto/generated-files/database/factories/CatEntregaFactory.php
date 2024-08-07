<?php

namespace Database\Factories;

use App\Models\CatEntrega;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatEntregaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatEntrega::class;

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
