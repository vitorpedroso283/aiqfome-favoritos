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
     * Perform a GET request to the FakeStore API and return the data as an array.
     *
     * Returns `null` if:
     * - The request failed (non-200 status).
     * - The response is not an array.
     * - The response does not contain an `id` key (product not found).
     *
     * @param  string  $endpoint
     * @return array|null
     */
    public function get(string $endpoint): array|null
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $response = Http::get($url);

        // Se não estiver OK ou estiver vazio, retorna null
        if (!$response->ok() || empty($response->body())) {
            return null;
        }

        $data = $response->json();

        if (!is_array($data) || !array_key_exists('id', $data)) {
            return null;
        }

        return $data;
    }

    /**
     * Get a specific product by ID, caching the result.
     */
    public function getProduct(int $productId): ?array
    {
        return Cache::remember("product:$productId", 86400, function () use ($productId) {
            $response = $this->get($productId);
            return $response ?: null;
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
