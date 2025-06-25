<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
});

it('retrieves a customer by id', function () {
    $customer = \App\Models\Customer::factory()->create([
        'name' => 'João Marinho',
        'email' => 'joao.marinho@test.com',
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/customers/{$customer->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]
        ])
        ->assertJson([
            'data' => [
                'name' => 'João Marinho',
                'email' => 'joao.marinho@test.com',
            ]
        ]);
});

it('fails when retrieving a non-existent customer', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/customers/99999");

    $response->assertStatus(404);
});
