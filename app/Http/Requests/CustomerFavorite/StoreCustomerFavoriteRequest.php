<?php

namespace App\Http\Requests\CustomerFavorite;

use App\Rules\Product\ProductExistsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerFavoriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer');
        
        return [
            'product_id' => [
                'required',
                'integer',
                app(ProductExistsRule::class),
                Rule::unique('customer_favorites', 'product_id')
                    ->where(function ($query) use ($customerId) {
                        return $query->where('customer_id', $customerId);
                    }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'O campo produto é obrigatório.',
            'product_id.integer' => 'O ID do produto deve ser um número inteiro válido.',
            'product_id.unique' => 'Este produto já foi adicionado aos favoritos desse cliente.',
        ];
    }
}
