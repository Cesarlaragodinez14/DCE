<?php

use App\Models\User;
use App\Models\CatDgsegEf;
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

test('it gets cat_dgseg_ef all_auditorias', function () {
    $catDgsegEf = CatDgsegEf::factory()->create();
    $allAuditorias = Auditorias::factory()
        ->count(2)
        ->create([
            'dgseg_ef' => $catDgsegEf->id,
        ]);

    $response = $this->getJson(
        route('api.cat-dgseg-efs.all-auditorias.index', $catDgsegEf)
    );

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the cat_dgseg_ef all_auditorias', function () {
    $catDgsegEf = CatDgsegEf::factory()->create();
    $data = Auditorias::factory()
        ->make([
            'dgseg_ef' => $catDgsegEf->id,
        ])
        ->toArray();

    $response = $this->postJson(
        route('api.cat-dgseg-efs.all-auditorias.store', $catDgsegEf),
        $data
    );

    unset($data['cuenta_publica']);
    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);

    $auditorias = Auditorias::latest('id')->first();

    $this->assertEquals($catDgsegEf->id, $auditorias->dgseg_ef);
});
