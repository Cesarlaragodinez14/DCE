<?php

namespace Database\Factories;

use App\Models\CatDgsegEf;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatDgsegEfFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatDgsegEf::class;

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
