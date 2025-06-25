<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListCustomerFavoritesRequest extends FormRequest
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
        return [
            'page'       => ['sometimes', 'integer', 'min:1'],
            'per_page'  => ['sometimes', 'integer', 'min:1', 'max:100'],
            'order_by'  => ['sometimes', 'string', 'in:product_id,created_at'],
            'order_dir' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }
}
