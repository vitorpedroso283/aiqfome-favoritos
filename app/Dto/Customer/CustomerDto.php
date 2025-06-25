<?php

namespace App\Dto\Customer;

class CustomerDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {
    }

    /**
     * Cria o DTO a partir de um array (ex.: Request validated).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email']
        );
    }
}
