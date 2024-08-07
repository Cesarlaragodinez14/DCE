<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatEnteDeLaAccion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_ente_de_la_accions list', function () {
    $catEnteDeLaAccions = CatEnteDeLaAccion::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-ente-de-la-accions.index'));

    $response->assertOk()->assertSee($catEnteDeLaAccions[0]->valor);
});

test('it stores the cat_ente_de_la_accion', function () {
    $data = CatEnteDeLaAccion::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(
        route('api.cat-ente-de-la-accions.store'),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_ente_de_la_accion', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_ente_de_la_accion', function () {
    $catEnteDeLaAccion = CatEnteDeLaAccion::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->word(),
        'activo' => fake()->word(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-ente-de-la-accions.update', $catEnteDeLaAccion),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catEnteDeLaAccion->id;

    $this->assertDatabaseHas('cat_ente_de_la_accion', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_ente_de_la_accion', function () {
    $catEnteDeLaAccion = CatEnteDeLaAccion::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-ente-de-la-accions.destroy', $catEnteDeLaAccion)
    );

    $this->assertModelMissing($catEnteDeLaAccion);

    $response->assertNoContent();
});
