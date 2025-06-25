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
