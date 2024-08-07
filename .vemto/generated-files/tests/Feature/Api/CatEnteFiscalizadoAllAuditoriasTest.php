<?php

use App\Models\User;
use App\Models\Auditorias;
use Laravel\Sanctum\Sanctum;
use App\Models\CatEnteFiscalizado;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_ente_fiscalizado all_auditorias', function () {
    $catEnteFiscalizado = CatEnteFiscalizado::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'ente_fiscalizado' => $catEnteFiscalizado->id,
        ]);

    $response = $this->getJson(
        route(
            'api.cat-ente-fiscalizados.all-auditorias.index',
            $catEnteFiscalizado
        )
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_ente_fiscalizado all_auditorias', function () {
    $catEnteFiscalizado = CatEnteFiscalizado::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'ente_fiscalizado' => $catEnteFiscalizado->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route(
            'api.cat-ente-fiscalizados.all-auditorias.store',
            $catEnteFiscalizado
        ),
        $data
    );

    unset($data['cuenta_publica']);
    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals($catEnteFiscalizado->id, $auditorias->ente_fiscalizado);
});
