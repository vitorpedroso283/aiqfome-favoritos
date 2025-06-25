<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],            // nome obrigatório
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'], // email obrigatório e único
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cliente é obrigatório.',
            'email.required' => 'O e-mail do cliente é obrigatório.',
            'email.email' => 'Forneça um e-mail válido.',
            'email.unique' => 'Este e-mail já está registrado.',
        ];
    }
}
