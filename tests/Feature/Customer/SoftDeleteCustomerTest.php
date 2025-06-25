<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
});

it('deletes a customer (soft delete)', function () {
    $customer = \App\Models\Customer::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/customers/{$customer->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('customers', [
        'id' => $customer->id,
    ]);
});

it('fails when trying to delete a non-existent customer', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/customers/99999");

    $response->assertStatus(404);
});
