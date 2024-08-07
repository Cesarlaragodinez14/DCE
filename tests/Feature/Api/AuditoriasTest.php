<?php

use App\Models\User;
use App\Models\CatUaa;
use App\Models\Auditorias;
use App\Models\CatEntrega;
use App\Models\CatDgsegEf;
use Laravel\Sanctum\Sanctum;
use App\Models\CatClaveAccion;
use App\Models\CatCuentaPublica;
use App\Models\CatEnteDeLaAccion;
use App\Models\CatTipoDeAuditoria;
use App\Models\CatEnteFiscalizado;
use App\Models\CatSiglasTipoAccion;
use App\Models\CatAuditoriaEspecial;
use App\Models\CatSiglasAuditoriaEspecial;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets all_auditorias list', function () {
    $allAuditorias = Auditorias::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.all-auditorias.index'));

    $response->assertOk()->assertSee($allAuditorias[0]->clave_de_accion);
});

test('it stores the auditorias', function () {
    $data = Auditorias::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(route('api.all-auditorias.store'), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the auditorias', function () {
    $auditorias = Auditorias::factory()->create();

    $catEntrega = CatEntrega::factory()->create();
    $catAuditoriaEspecial = CatAuditoriaEspecial::factory()->create();
    $catUaa = CatUaa::factory()->create();
    $catTipoDeAuditoria = CatTipoDeAuditoria::factory()->create();
    $catSiglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::factory()->create();
    $catEnteFiscalizado = CatEnteFiscalizado::factory()->create();
    $catEnteDeLaAccion = CatEnteDeLaAccion::factory()->create();
    $catClaveAccion = CatClaveAccion::factory()->create();
    $catSiglasTipoAccion = CatSiglasTipoAccion::factory()->create();
    $catDgsegEf = CatDgsegEf::factory()->create();
    $catCuentaPublica = CatCuentaPublica::factory()->create();

    $data = [
        'clave_de_accion' => fake()->word(),
        'titulo' => fake()->word(),
        'numero_de_auditoria' => fake()->word(),
        'nombre_director_general' => fake()->word(),
        'direccion_de_area' => fake()->word(),
        'nombre_director_de_area' => fake()->word(),
        'sub_direccion_de_area' => fake()->word(),
        'nombre_sub_director_de_area' => fake()->word(),
        'jefe_de_departamento' => fake()->word(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
        'entrega' => $catEntrega->id,
        'auditoria_especial' => $catAuditoriaEspecial->id,
        'siglas_dg_uaa' => $catUaa->id,
        'tipo_de_auditoria' => $catTipoDeAuditoria->id,
        'siglas_auditoria_especial' => $catSiglasAuditoriaEspecial->id,
        'ente_fiscalizado' => $catEnteFiscalizado->id,
        'ente_de_la_accion' => $catEnteDeLaAccion->id,
        'clave_accion' => $catClaveAccion->id,
        'siglas_tipo_accion' => $catSiglasTipoAccion->id,
        'dgseg_ef' => $catDgsegEf->id,
        'cuenta_publica' => $catCuentaPublica->id,
    ];

    $response = $this->putJson(
        route('api.all-auditorias.update', $auditorias),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $auditorias->id;

    $this->assertDatabaseHas('aditorias', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the auditorias', function () {
    $auditorias = Auditorias::factory()->create();

    $response = $this->deleteJson(
        route('api.all-auditorias.destroy', $auditorias)
    );

    $this->assertModelMissing($auditorias);

    $response->assertNoContent();
});
