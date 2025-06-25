<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerFavorite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerFavorite>
 */
class CustomerFavoriteFactory extends Factory
{
    protected $model = CustomerFavorite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'product_id' => $this->faker->numberBetween(1, 100), // Id do produto (ex.: 1–100 para testes)
        ];
    }
}
