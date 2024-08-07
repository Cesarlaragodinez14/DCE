<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\CatTipoDeAuditoria;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_tipo_de_auditorias list', function () {
    $catTipoDeAuditorias = CatTipoDeAuditoria::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-tipo-de-auditorias.index'));

    $response->assertOk()->assertSee($catTipoDeAuditorias[0]->valor);
});

test('it stores the cat_tipo_de_auditoria', function () {
    $data = CatTipoDeAuditoria::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(
        route('api.cat-tipo-de-auditorias.store'),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_tipo_de_auditoria', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_tipo_de_auditoria', function () {
    $catTipoDeAuditoria = CatTipoDeAuditoria::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->word(),
        'activo' => fake()->boolean(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-tipo-de-auditorias.update', $catTipoDeAuditoria),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catTipoDeAuditoria->id;

    $this->assertDatabaseHas('cat_tipo_de_auditoria', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_tipo_de_auditoria', function () {
    $catTipoDeAuditoria = CatTipoDeAuditoria::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-tipo-de-auditorias.destroy', $catTipoDeAuditoria)
    );

    $this->assertModelMissing($catTipoDeAuditoria);

    $response->assertNoContent();
});
