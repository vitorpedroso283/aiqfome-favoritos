<?php

use App\Models\Customer;
use App\Models\CustomerFavorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;

    $this->customer = Customer::factory()->create();

    // Fake para retorno do FakeStore
    Http::fake([
        'https://fakestoreapi.com/products/*' => Http::response([
            'id' => 1,
            'title' => 'Produto Teste',
            'price' => 99.99,
            'image' => 'https://exemplo.com/image.png',
            'rating' => [
                'rate' => 4.5,
                'count' => 200,
            ]
        ], 200),
    ]);
});

it('fails if customer does not exist when adding a favorite', function() {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson("/api/customers/9999/favorites", [
            'product_id' => 1,
        ]);

    $response->assertStatus(404);
});

it('fails if the product does not exist in FakeStore', function() {
    Http::fake([
        'https://fakestoreapi.com/products/*' => Http::response([], 404),
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson("/api/customers/{$this->customer->id}/favorites", [
            'product_id' => 1,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['product_id']);
});

it('fails if the favorite already exists for this customer', function() {
    CustomerFavorite::factory()->create([
        'customer_id' => $this->customer->id,
        'product_id' => 1,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson("/api/customers/{$this->customer->id}/favorites", [
            'product_id' => 1,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['product_id']);
});

it('creates a favorite successfully when all validations pass', function() {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson("/api/customers/{$this->customer->id}/favorites", [
            'product_id' => 1,
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'product_id',
                'title',
                'price',
                'image',
                'review' => [
                    'rate',
                    'count',
                ]
            ]
        ]);

    $this->assertDatabaseHas('customer_favorites', [
        'customer_id' => $this->customer->id,
        'product_id' => 1,
    ]);
});
