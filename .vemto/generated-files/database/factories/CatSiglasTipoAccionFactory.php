<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\CatSiglasTipoAccion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatSiglasTipoAccionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatSiglasTipoAccion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'valor' => fake()->name(),
            'description' => fake()->sentence(15),
            'activo' => fake()->word(),
        ];
    }
}
