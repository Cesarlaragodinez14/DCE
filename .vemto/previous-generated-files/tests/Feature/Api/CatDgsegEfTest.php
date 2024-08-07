<?php

use App\Models\User;
use App\Models\CatDgsegEf;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets cat_dgseg_efs list', function () {
    $catDgsegEfs = CatDgsegEf::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('api.cat-dgseg-efs.index'));

    $response->assertOk()->assertSee($catDgsegEfs[0]->valor);
});

test('it stores the cat_dgseg_ef', function () {
    $data = CatDgsegEf::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(route('api.cat-dgseg-efs.store'), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('cat_dgseg_ef', $data);

    $response->assertStatus(201)->assertJsonFragment($data);
});

test('it updates the cat_dgseg_ef', function () {
    $catDgsegEf = CatDgsegEf::factory()->create();

    $data = [
        'valor' => fake()->word(),
        'descripcion' => fake()->word(),
        'activo' => fake()->word(),
        'created_at' => fake()->dateTime(),
        'updated_at' => fake()->dateTime(),
    ];

    $response = $this->putJson(
        route('api.cat-dgseg-efs.update', $catDgsegEf),
        $data
    );

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $catDgsegEf->id;

    $this->assertDatabaseHas('cat_dgseg_ef', $data);

    $response->assertStatus(200)->assertJsonFragment($data);
});

test('it deletes the cat_dgseg_ef', function () {
    $catDgsegEf = CatDgsegEf::factory()->create();

    $response = $this->deleteJson(
        route('api.cat-dgseg-efs.destroy', $catDgsegEf)
    );

    $this->assertModelMissing($catDgsegEf);

    $response->assertNoContent();
});
