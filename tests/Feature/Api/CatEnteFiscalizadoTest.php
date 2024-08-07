<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatEnteFiscalizado;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_ente_fiscalizados list', function () {
    $catEnteFiscalizados = CatEnteFiscalizado::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-ente-fiscalizados.index'));

    $response->assertOk()->assertSee($catEnteFiscalizados[0]->valor);
});

test('it stores the cat_ente_fiscalizado', function () {
    $data = CatEnteFiscalizado::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(
        route('api.cat-ente-fiscalizados.store'),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_ente_fiscalizado', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_ente_fiscalizado', function () {
    $catEnteFiscalizado = CatEnteFiscalizado::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->sentence(15),
        'activo' => fake()->boolean(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-ente-fiscalizados.update', $catEnteFiscalizado),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catEnteFiscalizado->id;

    $this->assertDatabaseHas('cat_ente_fiscalizado', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_ente_fiscalizado', function () {
    $catEnteFiscalizado = CatEnteFiscalizado::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-ente-fiscalizados.destroy', $catEnteFiscalizado)
    );

    $this->assertModelMissing($catEnteFiscalizado);

    $response->assertNoContent();
});
