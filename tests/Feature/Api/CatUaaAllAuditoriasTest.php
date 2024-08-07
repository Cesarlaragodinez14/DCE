<?php

use App\Models\User;
use App\Models\CatUaa;
use App\Models\Auditorias;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_uaa all_auditorias', function () {
    $catUaa = CatUaa::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'siglas_dg_uaa' => $catUaa->id,
        ]);

    $response = $this->getJson(
        route('api.cat-uaas.all-auditorias.index', $catUaa)
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_uaa all_auditorias', function () {
    $catUaa = CatUaa::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'siglas_dg_uaa' => $catUaa->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route('api.cat-uaas.all-auditorias.store', $catUaa),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals($catUaa->id, $auditorias->siglas_dg_uaa);
});
