<?php

namespace App\Services\Product;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProductDataProvider
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.fakestore.url');
    }

    /**
     * Perform a GET request to the FakeStore API.
     */
    public function get(string $endpoint): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        return Http::get($url)->json();
    }

    /**
     * Get a specific product by ID, caching the result.
     */
    public function getProduct(int $productId): array
    {
        return Cache::remember("product:$productId", 86400, function () use ($productId) {
            $response = $this->get($productId);
            return $response ?: [];
        });
    }

    /**
     * List all products.
     */
    public function listProducts(): array
    {
        return Cache::remember("product:list", 86400, function () {
            $response = $this->get('');

            return $response ?: [];
        });
    }
}
