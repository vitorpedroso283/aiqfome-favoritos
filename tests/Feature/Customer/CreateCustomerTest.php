<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a customer with valid data', function () {
    $response = $this->postJson('/api/customers', [
        'name' => 'John Doe',
        'email' => 'john.doe@test.com',
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure(['id', 'name', 'email', 'created_at'])
             ->assertJson([
                 'name' => 'John Doe',
                 'email' => 'john.doe@test.com',
             ]);

    $this->assertDatabaseHas('customers', [
        'name' => 'John Doe',
        'email' => 'john.doe@test.com',
    ]);
});

it('fails if name is missing', function () {
    $response = $this->postJson('/api/customers', [
        'email' => 'no.name@test.com',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
});

it('fails if email is missing', function () {
    $response = $this->postJson('/api/customers', [
        'name' => 'John No Email',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
});

it('fails if email is duplicated', function () {
    \App\Models\Customer::factory()->create([
        'email' => 'duplicate@test.com',
    ]);

    $response = $this->postJson('/api/customers', [
        'name' => 'Another John',
        'email' => 'duplicate@test.com',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
});
