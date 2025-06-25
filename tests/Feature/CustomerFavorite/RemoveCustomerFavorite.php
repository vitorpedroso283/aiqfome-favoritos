<?php

use App\Models\Customer;
use App\Models\CustomerFavorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;

    $this->customer = Customer::factory()->create();
});

it('fails if the favorite does not exist for the customer', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/customers/{$this->customer->id}/favorites/999");

    $response->assertStatus(404);
});

it('removes a favorite successfully and cleans the cache', function () {
    $productId = 1;

    CustomerFavorite::factory()->create([
        'customer_id' => $this->customer->id,
        'product_id' => $productId,
    ]);

    $this->assertDatabaseHas('customer_favorites', [
        'customer_id' => $this->customer->id,
        'product_id' => $productId,
    ]);

    Cache::shouldReceive('forget')
        ->once()
        ->with("customer:{$this->customer->id}:favorites");

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/customers/{$this->customer->id}/favorites/{$productId}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('customer_favorites', [
        'customer_id' => $this->customer->id,
        'product_id' => $productId,
    ]);
});
