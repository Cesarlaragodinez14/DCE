<?php

use App\Models\User;
use App\Models\CatEntrega;
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

test('it gets cat_entrega all_auditorias', function () {
    $catEntrega = CatEntrega::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'entrega' => $catEntrega->id,
        ]);

    $response = $this->getJson(
        route('api.cat-entregas.all-auditorias.index', $catEntrega)
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_entrega all_auditorias', function () {
    $catEntrega = CatEntrega::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'entrega' => $catEntrega->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route('api.cat-entregas.all-auditorias.store', $catEntrega),
        $data
    );

    unset($data['cuenta_publica']);
    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals($catEntrega->id, $auditorias->entrega);
});
