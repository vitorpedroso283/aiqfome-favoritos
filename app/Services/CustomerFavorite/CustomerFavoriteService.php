<?php

namespace App\Services\CustomerFavorite;

use App\Models\CustomerFavorite;
use App\Services\Product\ProductDataProvider;
use Illuminate\Support\Facades\Cache;

class CustomerFavoriteService
{

    protected ProductDataProvider $productDataProvider;

    public function __construct(ProductDataProvider $productDataProvider)
    {
        $this->productDataProvider = $productDataProvider;
    }

    public function all(int $customerId, int $perPage = 10, string $orderBy = 'product_id', string $orderDir = 'asc')
    {
        $cacheKey = "customer:{$customerId}:favorites:{$orderBy}:{$orderDir}:{$perPage}:page_" . request('page', 1);

        return Cache::remember($cacheKey, 3600, function () use ($customerId, $orderBy, $orderDir, $perPage) {
            $favorites = CustomerFavorite::where('customer_id', $customerId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($perPage);

            $favorites->getCollection()->transform(function ($favorite) {
                $productData = $this->productDataProvider->getProduct($favorite->product_id);

                $favorite->title = $productData['title'] ?? null;
                $favorite->price = $productData['price'] ?? null;
                $favorite->image = $productData['image'] ?? null;

                $favorite->rating_rate = $productData['rating']['rate'] ?? null;
                $favorite->rating_count = $productData['rating']['count'] ?? null;

                return $favorite;
            });

            return $favorites;
        });
    }
}
