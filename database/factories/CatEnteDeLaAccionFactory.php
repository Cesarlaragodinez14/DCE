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
            'valor' => $faker->word,
            'descripcion' => $faker->sentence,
            'activo' => $faker->boolean, // Genera 0 o 1 para un campo booleano
        ];
    }
}
