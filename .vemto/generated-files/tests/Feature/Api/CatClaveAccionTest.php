<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatClaveAccion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_clave_accions list', function () {
    $catClaveAccions = CatClaveAccion::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-clave-accions.index'));

    $response->assertOk()->assertSee($catClaveAccions[0]->valor);
});

test('it stores the cat_clave_accion', function () {
    $data = CatClaveAccion::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(route('api.cat-clave-accions.store'), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_clave_accion', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_clave_accion', function () {
    $catClaveAccion = CatClaveAccion::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->word(),
        'activo' => fake()->word(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-clave-accions.update', $catClaveAccion),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catClaveAccion->id;

    $this->assertDatabaseHas('cat_clave_accion', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_clave_accion', function () {
    $catClaveAccion = CatClaveAccion::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-clave-accions.destroy', $catClaveAccion)
    );

    $this->assertModelMissing($catClaveAccion);

    $response->assertNoContent();
});
