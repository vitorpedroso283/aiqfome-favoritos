<?php

namespace App\Services\Customer;

use App\Dto\Customer\CustomerDto;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    public function create(CustomerDto $dto): Customer
    {
        try {
            return Customer::create([
                'name' => $dto->name,
                'email' => $dto->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating customer', [
                'name' => $dto->name,
                'email' => $dto->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function update(int $id, CustomerDto $dto): Customer
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->update([
                'name' => $dto->name,
                'email' => $dto->email,
            ]);

            return $customer;
        } catch (\Exception $e) {
            Log::error('Error updating customer', [
                'customer_id' => $id,
                'name' => $dto->name,
                'email' => $dto->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function find(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    public function all(int $perPage = 15, string $orderBy = 'id', string $orderDir = 'asc')
    {
        return Customer::orderBy($orderBy, $orderDir)->paginate($perPage);
    }
    
}
