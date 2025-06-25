<?php

namespace App\Dto\CustomerFavorite;

class StoreCustomerFavoriteDto
{
    public int $product_id;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->product_id = $data['product_id'];

        return $dto;
    }
}
