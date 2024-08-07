<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatCuentaPublica;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_cuenta_publicas list', function () {
    $catCuentaPublicas = CatCuentaPublica::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-cuenta-publicas.index'));

    $response->assertOk()->assertSee($catCuentaPublicas[0]->valor);
});

test('it stores the cat_cuenta_publica', function () {
    $data = CatCuentaPublica::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(route('api.cat-cuenta-publicas.store'), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_cuenta_publica', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_cuenta_publica', function () {
    $catCuentaPublica = CatCuentaPublica::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->sentence(15),
        'activo' => fake()->boolean(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-cuenta-publicas.update', $catCuentaPublica),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catCuentaPublica->id;

    $this->assertDatabaseHas('cat_cuenta_publica', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_cuenta_publica', function () {
    $catCuentaPublica = CatCuentaPublica::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-cuenta-publicas.destroy', $catCuentaPublica)
    );

    $this->assertModelMissing($catCuentaPublica);

    $response->assertNoContent();
});
