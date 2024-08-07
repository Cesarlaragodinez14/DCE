<?php

use App\Models\User;
use App\Models\CatUaa;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_uaas list', function () {
    $catUaas = CatUaa::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-uaas.index'));

    $response->assertOk()->assertSee($catUaas[0]->valor);
});

test('it stores the cat_uaa', function () {
    $data = CatUaa::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(route('api.cat-uaas.store'), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_uaa', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_uaa', function () {
    $catUaa = CatUaa::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->word(),
        'activo' => fake()->boolean(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(route('api.cat-uaas.update', $catUaa), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catUaa->id;

    $this->assertDatabaseHas('cat_uaa', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_uaa', function () {
    $catUaa = CatUaa::factory()->create();

    $response = $this->deleteJson(route('api.cat-uaas.destroy', $catUaa));

    $this->assertModelMissing($catUaa);

    $response->assertNoContent();
});
