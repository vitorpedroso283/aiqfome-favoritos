<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
});

it('creates a customer with valid data', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/customers', [
            'name' => 'Fake User',
            'email' => 'fake.email@test.com',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
            ]
        ])
        ->assertJson([
            'data' => [
                'name' => 'Fake User',
                'email' => 'fake.email@test.com',
            ]
        ]);

    $this->assertDatabaseHas('customers', [
        'name' => 'Fake User',
        'email' => 'fake.email@test.com',
    ]);
});

it('fails if name is missing', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/customers', [
            'email' => 'no.name@test.com',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('fails if email is missing', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/customers', [
            'name' => 'Fake User No Email',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('fails if email is duplicated', function () {
    \App\Models\Customer::factory()->create([
        'email' => 'duplicate@test.com',
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/customers', [
            'name' => 'Another User',
            'email' => 'duplicate@test.com',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
