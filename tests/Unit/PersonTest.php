<?php

namespace Tests\Unit\DTO;

use App\DTO\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function test_instantiated_dto_with_correct_values()
    {
        $person = new Person(
            title: 'Mr',
            firstName: 'John',
            initial: null,
            lastName: 'Smith'
        );

        $this->assertSame('Mr', $person->title);
        $this->assertSame('John', $person->firstName);
        $this->assertNull($person->initial);
        $this->assertSame('Smith', $person->lastName);
    }

    public function test_serialized_dto_to_array_with_correct_keys()
    {
        $person = new Person(
            title: 'Mrs',
            firstName: null,
            initial: 'J',
            lastName: 'Doe'
        );

        $expected = [
            'title' => 'Mrs',
            'first_name' => null,
            'initial' => 'J',
            'last_name' => 'Doe',
        ];

        $this->assertSame($expected, $person->toArray());
        $this->assertSame($expected, $person->jsonSerialize());
    }
}
