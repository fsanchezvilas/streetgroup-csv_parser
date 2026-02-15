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
            lastName: 'Smith',
            initial: null
        );

        $this->assertSame('Mr', $person->title);
        $this->assertSame('John', $person->firstName);
        $this->assertSame('Smith', $person->lastName);
	    $this->assertNull($person->initial);
    }

    public function test_serialized_dto_to_array_with_correct_keys()
    {
        $person = new Person(
            title: 'Mrs',
            firstName: null,
            lastName: 'Doe',
	        initial: 'J'
        );

        $expected = [
            'title' => 'Mrs',
            'first_name' => null,
            'last_name' => 'Doe',
	        'initial' => 'J'

        ];

        $this->assertSame($expected, $person->toArray());
        $this->assertSame($expected, $person->jsonSerialize());
    }
}
