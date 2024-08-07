<?php

use App\Models\User;
use App\Models\Auditorias;
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

test('it gets cat_siglas_tipo_accion all_auditorias', function () {
    $catSiglasTipoAccion = CatSiglasTipoAccion::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'siglas_tipo_accion' => $catSiglasTipoAccion->id,
        ]);

    $response = $this->getJson(
        route(
            'api.cat-siglas-tipo-acciones.all-auditorias.index',
            $catSiglasTipoAccion
        )
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_siglas_tipo_accion all_auditorias', function () {
    $catSiglasTipoAccion = CatSiglasTipoAccion::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'siglas_tipo_accion' => $catSiglasTipoAccion->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route(
            'api.cat-siglas-tipo-acciones.all-auditorias.store',
            $catSiglasTipoAccion
        ),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals(
        $catSiglasTipoAccion->id,
        $auditorias->siglas_tipo_accion
    );
});
