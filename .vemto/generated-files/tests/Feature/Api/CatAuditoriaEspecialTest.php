<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatAuditoriaEspecial;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_auditoria_especials list', function () {
    $catAuditoriaEspecials = CatAuditoriaEspecial::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-auditoria-especials.index'));

    $response->assertOk()->assertSee($catAuditoriaEspecials[0]->valor);
});

test('it stores the cat_auditoria_especial', function () {
    $data = CatAuditoriaEspecial::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(
        route('api.cat-auditoria-especials.store'),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_auditoria_especial', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_auditoria_especial', function () {
    $catAuditoriaEspecial = CatAuditoriaEspecial::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->sentence(15),
        'activo' => fake()->boolean(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-auditoria-especials.update', $catAuditoriaEspecial),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catAuditoriaEspecial->id;

    $this->assertDatabaseHas('cat_auditoria_especial', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_auditoria_especial', function () {
    $catAuditoriaEspecial = CatAuditoriaEspecial::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-auditoria-especials.destroy', $catAuditoriaEspecial)
    );

    $this->assertModelMissing($catAuditoriaEspecial);

    $response->assertNoContent();
});
