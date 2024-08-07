<?php

use App\Models\User;
use App\Models\Auditorias;
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

test('it gets cat_tipo_de_auditoria all_auditorias', function () {
    $catTipoDeAuditoria = CatTipoDeAuditoria::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'tipo_de_auditoria' => $catTipoDeAuditoria->id,
        ]);

    $response = $this->getJson(
        route(
            'api.cat-tipo-de-auditorias.all-auditorias.index',
            $catTipoDeAuditoria
        )
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_tipo_de_auditoria all_auditorias', function () {
    $catTipoDeAuditoria = CatTipoDeAuditoria::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'tipo_de_auditoria' => $catTipoDeAuditoria->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route(
            'api.cat-tipo-de-auditorias.all-auditorias.store',
            $catTipoDeAuditoria
        ),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals(
        $catTipoDeAuditoria->id,
        $auditorias->tipo_de_auditoria
    );
});
