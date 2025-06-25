<?php

namespace App\Services\CustomerFavorite;

use App\Dto\CustomerFavorite\StoreCustomerFavoriteDto;
use App\Models\CustomerFavorite;
use App\Services\Product\ProductDataProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomerFavoriteService
{

    protected ProductDataProvider $productDataProvider;

    public function __construct(ProductDataProvider $productDataProvider)
    {
        $this->productDataProvider = $productDataProvider;
    }

    public function all(int $customerId, int $perPage = 10, string $orderBy = 'product_id', string $orderDir = 'asc')
    {
        $currentPage = request('page', 1);
        $cacheKey = "customer:{$customerId}:favorites:{$orderBy}:{$orderDir}:{$perPage}:page_{$currentPage}";
        $cacheIndexKey = "customer:{$customerId}:favorites:keys";

        $keys = Cache::get($cacheIndexKey, []);
        if (!in_array($cacheKey, $keys)) {
            $keys[] = $cacheKey;
            Cache::forever($cacheIndexKey, $keys);
        }

        return Cache::remember($cacheKey, 3600, function () use ($customerId, $orderBy, $orderDir, $perPage) {
            $favorites = CustomerFavorite::where('customer_id', $customerId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($perPage);

            $favorites->getCollection()->transform(function ($favorite) {
                $productData = $this->productDataProvider->getProduct($favorite->product_id);

                // Se não retornar nada, remove o favorito
                if (empty($productData)) {
                    $favorite->delete();
                    return null;
                }

                $favorite->title = $productData['title'] ?? null;
                $favorite->price = $productData['price'] ?? null;
                $favorite->image = $productData['image'] ?? null;

                $favorite->rating_rate = $productData['rating']['rate'] ?? null;
                $favorite->rating_count = $productData['rating']['count'] ?? null;

                return $favorite;
            });

            // Filtra nulls antes de retornar
            $favorites->setCollection($favorites->getCollection()->filter());

            return $favorites;
        });
    }

    public function add(int $customerId, StoreCustomerFavoriteDto $dto): CustomerFavorite
    {
        try {
            $this->forgetAllCustomerFavoritesCache($customerId);

            $favorite = CustomerFavorite::create([
                'customer_id' => $customerId,
                'product_id' => $dto->product_id,
            ]);

            $productData = $this->productDataProvider->getProduct($dto->product_id);

            $favorite->title = $productData['title'] ?? null;
            $favorite->price = $productData['price'] ?? null;
            $favorite->image = $productData['image'] ?? null;

            $favorite->rating_rate = $productData['rating']['rate'] ?? null;
            $favorite->rating_count = $productData['rating']['count'] ?? null;

            return $favorite;
        } catch (\Exception $e) {
            Log::error('Error adding favorite', [
                'customer_id' => $customerId,
                'product_id' => $dto->product_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function remove(int $customerId, int $productId): bool
    {
        try {
            $this->forgetAllCustomerFavoritesCache($customerId);

            $favorite = CustomerFavorite::where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->first();

            if (!$favorite) {
                return false;
            }

            $favorite->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Error removing favorite', [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function forgetAllCustomerFavoritesCache(int $customerId): void
    {
        $cacheIndexKey = "customer:{$customerId}:favorites:keys";
        $keys = Cache::get($cacheIndexKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($cacheIndexKey);
    }
}
