<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
});

it('updates a customer with valid data', function () {
    $customer = \App\Models\Customer::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@test.com',
    ]);

    $payload = [
        'name' => 'Updated Name',
        'email' => 'updated@test.com',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/customers/{$customer->id}", $payload);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'updated_at',
            ]
        ])
        ->assertJson([
            'data' => [
                'name' => 'Updated Name',
                'email' => 'updated@test.com',
            ]
        ]);

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Name',
        'email' => 'updated@test.com',
    ]);
});

it('fails if name is missing when updating', function () {
    $customer = \App\Models\Customer::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/customers/{$customer->id}", [
            'email' => 'no.name@test.com',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('fails if email is missing when updating', function () {
    $customer = \App\Models\Customer::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/customers/{$customer->id}", [
            'name' => 'Updated Name',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('fails if email is duplicated when updating', function () {
    $existing = \App\Models\Customer::factory()->create([
        'email' => 'duplicate@test.com',
    ]);

    $customer = \App\Models\Customer::factory()->create([
        'email' => 'other@test.com',
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/customers/{$customer->id}", [
            'name' => 'Updated Name',
            'email' => 'duplicate@test.com',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
