<?php

namespace Domain\Order\DTOs;

use Support\Traits\Makeable;

class OrderCustomerDTO
{
    use Makeable;

    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $city,
        public readonly string $address,
    ) {
    }

    public function fullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public static function fromArray(array $array): self
    {
        return self::make(
            $array['first_name'] ?? '',
            $array['last_name'] ?? '',
            $array['email'] ?? '',
            $array['phone'] ?? '',
            $array['city'] ?? '',
            $array['address'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
        ];
    }
}
