<?php

namespace App\Http\Resources\CustomerFavorite;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerFavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'title' => $this->title,
            'price' => $this->price,
            'image' => $this->image,
            'review' => [
                'rate' => $this->rating_rate,
                'count' => $this->rating_count,
            ],
        ];
    }
}
