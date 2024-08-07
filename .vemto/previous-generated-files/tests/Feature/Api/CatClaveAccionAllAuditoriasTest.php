<?php

use App\Models\User;
use App\Models\Auditorias;
use Laravel\Sanctum\Sanctum;
use App\Models\CatClaveAccion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_clave_accion all_auditorias', function () {
    $catClaveAccion = CatClaveAccion::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'clave_accion' => $catClaveAccion->id,
        ]);

    $response = $this->getJson(
        route('api.cat-clave-accions.all-auditorias.index', $catClaveAccion)
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_clave_accion all_auditorias', function () {
    $catClaveAccion = CatClaveAccion::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'clave_accion' => $catClaveAccion->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route('api.cat-clave-accions.all-auditorias.store', $catClaveAccion),
        $data
    );

    unset($data['cuenta_publica']);
    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals($catClaveAccion->id, $auditorias->clave_accion);
});
