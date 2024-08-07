<?php

use App\Models\User;
use App\Models\Auditorias;
use Laravel\Sanctum\Sanctum;
use App\Models\CatEnteDeLaAccion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_ente_de_la_accion all_auditorias', function () {
    $catEnteDeLaAccion = CatEnteDeLaAccion::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'ente_de_la_accion' => $catEnteDeLaAccion->id,
        ]);

    $response = $this->getJson(
        route(
            'api.cat-ente-de-la-accions.all-auditorias.index',
            $catEnteDeLaAccion
        )
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_ente_de_la_accion all_auditorias', function () {
    $catEnteDeLaAccion = CatEnteDeLaAccion::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'ente_de_la_accion' => $catEnteDeLaAccion->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route(
            'api.cat-ente-de-la-accions.all-auditorias.store',
            $catEnteDeLaAccion
        ),
        $data
    );

    unset($data['cuenta_publica']);
    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals($catEnteDeLaAccion->id, $auditorias->ente_de_la_accion);
});
