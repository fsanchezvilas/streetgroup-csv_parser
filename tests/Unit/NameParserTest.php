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
        $parser = new NameParser();
        $result = $parser->parse('Mr John Smith');

	    $this->assertCount(1, $result);
	    $this->assertSame('Mr', $result[0]->title);
	    $this->assertSame('John', $result[0]->firstName);
	    $this->assertSame('Smith', $result[0]->lastName);
	    $this->assertNull($result[0]->initial);
    }
}
