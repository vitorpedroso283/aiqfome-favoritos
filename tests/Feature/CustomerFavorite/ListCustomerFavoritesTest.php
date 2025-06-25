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

    // Criamos 12 favoritos para verificar paginação
    CustomerFavorite::factory()
        ->count(12)
        ->state(['customer_id' => $this->customer->id])
        ->create([
            'product_id' => 1,
        ]);
});

it('lists favorites with pagination and fetches details from FakeStore', function () {
    // Simula retorno da FakeStore para produto 1
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
                    'rating' => [
                        'rate',
                        'count',
                    ],
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
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ]
        ]);

    // Verifica que temos 10 registros por página
    $this->assertCount(10, $response->json('data'));
    $this->assertEquals(12, $response->json('meta.total'));
});
