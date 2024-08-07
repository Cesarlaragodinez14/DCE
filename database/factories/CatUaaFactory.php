<?php

namespace Database\Factories;

use App\Models\CatUaa;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatUaaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatUaa::class;

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
