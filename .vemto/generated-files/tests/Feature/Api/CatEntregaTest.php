<?php

use App\Models\User;
use App\Models\CatEntrega;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_entregas list', function () {
    $catEntregas = CatEntrega::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-entregas.index'));

    $response->assertOk()->assertSee($catEntregas[0]->valor);
});

test('it stores the cat_entrega', function () {
    $data = CatEntrega::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(route('api.cat-entregas.store'), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_entrega', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_entrega', function () {
    $catEntrega = CatEntrega::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->sentence(15),
        'activo' => fake()->boolean(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-entregas.update', $catEntrega),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catEntrega->id;

    $this->assertDatabaseHas('cat_entrega', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_entrega', function () {
    $catEntrega = CatEntrega::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-entregas.destroy', $catEntrega)
    );

    $this->assertModelMissing($catEntrega);

    $response->assertNoContent();
});
