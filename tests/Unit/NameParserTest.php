<?php

namespace Tests\Unit;

use App\Services\NameParser;
use PHPUnit\Framework\TestCase;

class NameParserTest extends TestCase
{
    /**
     * Test parsing a simple name: Title Firstname Lastname Initial
     */
    public function test_parses_simple_name()
    {
        //TODO: Create logic to implemente NameParser as a service - It will fail until the logic is implemented
        $parser = new NameParser();

        //TODO Im going to add a DTO for now we use the unit test to try the red-green-refactor
        $result = $parser->parse('Mr John Smith');

        $this->assertCount(1, $result);
        $this->assertEquals('Mr', $result[0]->title);
        $this->assertEquals('John', $result[0]->first_name);
        $this->assertEquals('Smith', $result[0]->last_name);
        $this->assertNull($result[0]->initial);
    }
}
