<?php

namespace App\DTO;

use JsonSerializable;

class Person implements JsonSerializable
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $firstName,
        public readonly string $lastName,
        public readonly ?string $initial,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
	        'initial' => $this->initial,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
