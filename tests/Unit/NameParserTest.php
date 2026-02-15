<?php

	namespace Tests\Unit;

	use App\Services\NameParser;
	use PHPUnit\Framework\TestCase;

	class NameParserTest extends TestCase
	{
		/**
		 * Test parsing a simple name: Title Firstname Lastname
		 */
		public function test_parses_simple_name(): void
		{
			$parser = new NameParser();
			$result = $parser->parse('Mr John Smith');

			$this->assertCount(1, $result);
			$this->assertSame('Mr', $result[0]->title);
			$this->assertSame('John', $result[0]->firstName);
			$this->assertSame('Smith', $result[0]->lastName);
			$this->assertNull($result[0]->initial);
		}

		/**
		 * Test parsing when the middle token is an initial (with or without a dot).
		 */
		public function test_parses_initial_instead_of_first_name(): void
		{
			$parser = new NameParser();

			$result = $parser->parse('Mr M Mackie');
			$this->assertCount(1, $result);
			$this->assertSame('Mr', $result[0]->title);
			$this->assertNull($result[0]->firstName);
			$this->assertSame('Mackie', $result[0]->lastName);
			$this->assertSame('M', $result[0]->initial);

			$result = $parser->parse('Mr F. Fredrickson');
			$this->assertCount(1, $result);
			$this->assertSame('Mr', $result[0]->title);
			$this->assertNull($result[0]->firstName);
			$this->assertSame('Fredrickson', $result[0]->lastName);
			$this->assertSame('F', $result[0]->initial);

		}

		/**
		 * Test parsing a couple sharing only the last name: "Mr and Mrs Smith".
		 */
		public function test_parses_couple_shared_last_name(): void
		{
			$parser = new NameParser();
			$result = $parser->parse('Mr and Mrs Smith');

			$this->assertCount(2, $result);

			$this->assertSame('Mr', $result[0]->title);
			$this->assertNull($result[0]->firstName);
			$this->assertSame('Smith', $result[0]->lastName);
			$this->assertNull($result[0]->initial);

			$this->assertSame('Mrs', $result[1]->title);
			$this->assertNull($result[1]->firstName);
			$this->assertSame('Smith', $result[1]->lastName);
			$this->assertNull($result[1]->initial);

		}

		/**
		 * Test parsing two full people in one field joined by "and".
		 */
		public function test_parses_two_people_joined_by_and(): void
		{
			$parser = new NameParser();
			$result = $parser->parse('Mr Tom Staff and Mr John Doe');

			$this->assertCount(2, $result);

			$this->assertSame('Mr', $result[0]->title);
			$this->assertSame('Tom', $result[0]->firstName);
			$this->assertSame('Staff', $result[0]->lastName);
			$this->assertNull($result[0]->initial);

			$this->assertSame('Mr', $result[1]->title);
			$this->assertSame('John', $result[1]->firstName);
			$this->assertSame('Doe', $result[1]->lastName);
			$this->assertNull($result[1]->initial);
		}

		/**
		 * Test parsing "Title1 & Title2 First Last" where both share the same name.
		 */
		public function test_parses_ampersand_couple_sharing_full_name(): void
		{
			$parser = new NameParser();
			$result = $parser->parse('Dr & Mrs Joe Bloggs');

			$this->assertCount(2, $result);

			$this->assertSame('Dr', $result[0]->title);
			$this->assertSame('Joe', $result[0]->firstName);
			$this->assertSame('Bloggs', $result[0]->lastName);
			$this->assertNull($result[0]->initial);

			$this->assertSame('Mrs', $result[1]->title);
			$this->assertSame('Joe', $result[1]->firstName);
			$this->assertSame('Bloggs', $result[1]->lastName);
			$this->assertNull($result[1]->initial);
		}

		/**
		 * Test parsing a hyphenated last name.
		 */
		public function test_parses_hyphenated_last_name(): void
		{
			$parser = new NameParser();
			$result = $parser->parse('Mrs Faye Hughes-Eastwood');

			$this->assertCount(1, $result);
			$this->assertSame('Mrs', $result[0]->title);
			$this->assertSame('Faye', $result[0]->firstName);
			$this->assertSame('Hughes-Eastwood', $result[0]->lastName);
			$this->assertNull($result[0]->initial);
		}
	}