<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerFavorite;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;

    $this->customer = Customer::factory()->create();

    // Criamos 12 favoritos com ids diferentes para verificar corretamente
    foreach (range(1, 12) as $productId) {
        CustomerFavorite::factory()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $productId,
        ]);
    }
});

it('lists favorites with pagination and fetches details from FakeStore', function () {
    Http::fake(function ($request) {
        $url = $request->url();
        $productId = (int) str_replace('https://fakestoreapi.com/products/', '', $url);

        return Http::response([
            'id' => $productId,
            'title' => "Produto Teste {$productId}",
            'price' => 99.99,
            'image' => "https://exemplo.com/image.png",
            'review' => [
                'rate' => 4.5,
                'count' => 200,
            ]
        ], 200);
    });

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/customers/{$this->customer->id}/favorites");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'product_id',
                    'title',
                    'price',
                    'image',
                    'review' => [
                        'rate',
                        'count',
                    ]
                ]
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ]
        ]);

    // Verifica paginação correta
    $this->assertCount(10, $response->json('data')); // Primeira page
    $this->assertEquals(12, $response->json('meta.total')); // Total de registros
});
