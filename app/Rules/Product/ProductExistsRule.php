<?php

namespace App\Rules\Product;

use App\Services\Product\ProductDataProvider;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductExistsRule implements ValidationRule
{

    protected ProductDataProvider $productDataProvider;

    public function __construct(ProductDataProvider $productDataProvider)
    {
        $this->productDataProvider = $productDataProvider;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = $this->productDataProvider->getProduct((int) $value);
        
        if (is_null($product)) {
            $fail('O produto informado não existe.');
        }
    }
}
