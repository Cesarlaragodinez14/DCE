<?php

use App\Models\User;
use App\Models\Auditorias;
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

test('it gets cat_auditoria_especial all_auditorias', function () {
    $catAuditoriaEspecial = CatAuditoriaEspecial::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'auditoria_especial' => $catAuditoriaEspecial->id,
        ]);

    $response = $this->getJson(
        route(
            'api.cat-auditoria-especials.all-auditorias.index',
            $catAuditoriaEspecial
        )
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_auditoria_especial all_auditorias', function () {
    $catAuditoriaEspecial = CatAuditoriaEspecial::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'auditoria_especial' => $catAuditoriaEspecial->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route(
            'api.cat-auditoria-especials.all-auditorias.store',
            $catAuditoriaEspecial
        ),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals(
        $catAuditoriaEspecial->id,
        $auditorias->auditoria_especial
    );
});
