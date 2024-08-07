<?php

use App\Models\User;
use App\Models\Auditorias;
use Laravel\Sanctum\Sanctum;
use App\Models\CatCuentaPublica;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_cuenta_publica all_auditorias', function () {
    $catCuentaPublica = CatCuentaPublica::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'cuenta_publica' => $catCuentaPublica->id,
        ]);

    $response = $this->getJson(
        route('api.cat-cuenta-publicas.all-auditorias.index', $catCuentaPublica)
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_cuenta_publica all_auditorias', function () {
    $catCuentaPublica = CatCuentaPublica::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'cuenta_publica' => $catCuentaPublica->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route(
            'api.cat-cuenta-publicas.all-auditorias.store',
            $catCuentaPublica
        ),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals($catCuentaPublica->id, $auditorias->cuenta_publica);
});
