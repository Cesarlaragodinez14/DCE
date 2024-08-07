<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatSiglasTipoAccion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_siglas_tipo_acciones list', function () {
    $catSiglasTipoAcciones = CatSiglasTipoAccion::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-siglas-tipo-acciones.index'));

    $response->assertOk()->assertSee($catSiglasTipoAcciones[0]->valor);
});

test('it stores the cat_siglas_tipo_accion', function () {
    $data = CatSiglasTipoAccion::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(
        route('api.cat-siglas-tipo-acciones.store'),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_siglas_tipo_accion', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_siglas_tipo_accion', function () {
    $catSiglasTipoAccion = CatSiglasTipoAccion::factory()->create();

    $data = [
        'valor' => fake()->name(),
        'description' => fake()->sentence(15),
        'activo' => fake()->word(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-siglas-tipo-acciones.update', $catSiglasTipoAccion),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catSiglasTipoAccion->id;

    $this->assertDatabaseHas('cat_siglas_tipo_accion', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_siglas_tipo_accion', function () {
    $catSiglasTipoAccion = CatSiglasTipoAccion::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-siglas-tipo-acciones.destroy', $catSiglasTipoAccion)
    );

    $this->assertModelMissing($catSiglasTipoAccion);

    $response->assertNoContent();
});
