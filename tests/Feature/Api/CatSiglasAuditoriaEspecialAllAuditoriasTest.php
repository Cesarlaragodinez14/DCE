<?php

use App\Models\User;
use App\Models\Auditorias;
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

test('it gets cat_siglas_auditoria_especial all_auditorias', function () {
    $catSiglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'siglas_auditoria_especial' => $catSiglasAuditoriaEspecial->id,
        ]);

    $response = $this->getJson(
        route(
            'api.cat-siglas-auditoria-especials.all-auditorias.index',
            $catSiglasAuditoriaEspecial
        )
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_siglas_auditoria_especial all_auditorias', function () {
    $catSiglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'siglas_auditoria_especial' => $catSiglasAuditoriaEspecial->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route(
            'api.cat-siglas-auditoria-especials.all-auditorias.store',
            $catSiglasAuditoriaEspecial
        ),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals(
        $catSiglasAuditoriaEspecial->id,
        $auditorias->siglas_auditoria_especial
    );
});
