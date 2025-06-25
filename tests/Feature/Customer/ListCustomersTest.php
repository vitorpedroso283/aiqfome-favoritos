<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
});

it('lists all customers', function () {
    \App\Models\Customer::factory()->count(3)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/customers");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);

    $this->assertEquals(3, count($response->json('data')));
});


it('respects the per_page parameter when listing customers', function () {
    \App\Models\Customer::factory()->count(30)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/customers?per_page=5");

    $response->assertStatus(200);
    $this->assertEquals(5, count($response->json('data')));
    $this->assertEquals(5, $response->json('meta.per_page'));
});

it('orders customers by name ascending', function () {
    \App\Models\Customer::factory()->create(['name' => 'Alice']);
    \App\Models\Customer::factory()->create(['name' => 'Bob']);
    \App\Models\Customer::factory()->create(['name' => 'Carol']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/customers?order_by=name&order_dir=asc");

    $names = array_column($response->json('data'), 'name');
    $this->assertEquals(['Alice', 'Bob', 'Carol'], $names);
});

it('orders customers by name in descending order', function () {
    \App\Models\Customer::factory()->create(['name' => 'Alice']);
    \App\Models\Customer::factory()->create(['name' => 'Bob']);
    \App\Models\Customer::factory()->create(['name' => 'Carol']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/customers?order_by=name&order_dir=desc");

    $names = array_column($response->json('data'), 'name');
    $this->assertEquals(['Carol', 'Bob', 'Alice'], $names);
});
