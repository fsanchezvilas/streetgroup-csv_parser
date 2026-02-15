<?php

namespace App\DTO;

use JsonSerializable;

class Person implements JsonSerializable
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $firstName,
        public readonly ?string $initial,
        public readonly string $lastName,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'first_name' => $this->firstName,
            'initial' => $this->initial,
            'last_name' => $this->lastName,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
