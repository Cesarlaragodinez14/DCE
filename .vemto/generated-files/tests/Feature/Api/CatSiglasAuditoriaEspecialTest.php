<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatSiglasAuditoriaEspecial;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_siglas_auditoria_especials list', function () {
    $catSiglasAuditoriaEspecials = CatSiglasAuditoriaEspecial::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-siglas-auditoria-especials.index'));

    $response->assertOk()->assertSee($catSiglasAuditoriaEspecials[0]->valor);
});

test('it stores the cat_siglas_auditoria_especial', function () {
    $data = CatSiglasAuditoriaEspecial::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(
        route('api.cat-siglas-auditoria-especials.store'),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_siglas_auditoria_especial', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_siglas_auditoria_especial', function () {
    $catSiglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->word(),
        'activo' => fake()->boolean(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route(
            'api.cat-siglas-auditoria-especials.update',
            $catSiglasAuditoriaEspecial
        ),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catSiglasAuditoriaEspecial->id;

    $this->assertDatabaseHas('cat_siglas_auditoria_especial', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_siglas_auditoria_especial', function () {
    $catSiglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::factory()->create();

    $response = $this->deleteJson(
        route(
            'api.cat-siglas-auditoria-especials.destroy',
            $catSiglasAuditoriaEspecial
        )
    );

    $this->assertModelMissing($catSiglasAuditoriaEspecial);

    $response->assertNoContent();
});
