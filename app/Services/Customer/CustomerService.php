<?php

namespace App\Services\Customer;

use App\Dto\Customer\CustomerDto;
use App\Models\Customer;

class CustomerService
{
    public function create(CustomerDto $dto): Customer
    {
        return Customer::create([
            'name' => $dto->name,
            'email' => $dto->email,
        ]);
    }
}
