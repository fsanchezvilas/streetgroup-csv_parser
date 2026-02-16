<?php

	namespace App\Services;

	use App\DTO\Person;
	use Illuminate\Support\Str;

	class NameParser
	{
		private const TITLES = ['Mr', 'Mrs', 'Mister', 'Ms', 'Dr', 'Prof'];
		private const JOINERS = ['and', '&'];

		/**
		 * Parse one raw name string into one or more people.
		 *
		 * This parser is intentionally limited to the patterns present in the fixture:
		 * - "Mr John Smith"
		 * - "Mr M Mackie" / "Mr F. Fredrickson"
		 * - "Mr and Mrs Smith"
		 * - "Mr Tom Staff and Mr John Doe"
		 * - "Dr & Mrs Joe Bloggs"
		 *
		 * @return array<int, Person>
		 */
		public function parse(string $name): array
		{
			$name = trim($name);

			if ($name === '') {
				return [];
			}

			// Rule 1: Shared last name couple: "Mr and Mrs Smith"
			$people = $this->tryParseMrAndMrsSharedLastName($name);
			if ($people !== null) {
				return $people;
			}

			// Rule 2: Shared full name with ampersand: "Dr & Mrs Joe Bloggs"
			$people = $this->tryParseAmpersandSharedName($name);
			if ($people !== null) {
				return $people;
			}

			// Rule 3: Two independent people: "Mr Tom Staff and Mr John Doe"
			$people = $this->tryParseTwoPeopleJoined($name);
			if ($people !== null) {
				return $people;
			}

			// Rule 4: Single person: "Title Middle Last"
			return $this->parseSingle($name);
		}

		/**
		 * @return array<int, Person>|null
		 */
		private function tryParseMrAndMrsSharedLastName(string $name): ?array
		{
			$tokens = preg_split('/\s+/', trim($name)) ?: [];

			if (! $this->isMrAndMrsSharedLastName($tokens)) {
				return null;
			}

			return $this->makeMrAndMrsSharedLastName($tokens[3]);
		}

		/**
		 * Case: "Dr & Mrs Joe Bloggs"
		 *
		 * @return array<int, Person>|null
		 */
		private function tryParseAmpersandSharedName(string $name): ?array
		{
			if (! Str::contains($name, ' & ')) {
				return null;
			}

			$pair = $this->splitByJoiner($name, '&');
			if ($pair === null) {
				return null;
			}

			$leftTitle = trim($pair[0]);
			$rightPeople = $this->parseSingle($pair[1]);

			if (count($rightPeople) !== 1) {
				return null;
			}

			$person = $rightPeople[0];

			return [
					new Person(
							title: $leftTitle,
							firstName: $person->firstName,
							lastName: $person->lastName,
							initial: $person->initial,
					),
					$person,
			];
		}

		/**
		 * Case: "Mr Tom Staff and Mr John Doe"
		 *
		 * @return array<int, Person>|null
		 */
		private function tryParseTwoPeopleJoined(string $name): ?array
		{
			$joiner = $this->detectJoiner($name);
			if ($joiner === null) {
				return null;
			}

			// Avoid treating "Mr and Mrs Smith" as two independent people.
			if ($joiner === 'and') {
				$tokens = preg_split('/\s+/', trim($name)) ?: [];
				if ($this->isMrAndMrsSharedLastName($tokens)) {
					return null;
				}
			}

			$pair = $this->splitByJoiner($name, $joiner);
			if ($pair === null) {
				return null;
			}

			// Ampersand special case is handled earlier.
			if ($joiner === '&') {
				return null;
			}

			return [
					...$this->parseSingle($pair[0]),
					...$this->parseSingle($pair[1]),
			];
		}

		/**
		 * Tokens: ["Mr", "and", "Mrs", "Smith"]
		 *
		 * @param array<int, string> $tokens
		 */
		private function isMrAndMrsSharedLastName(array $tokens): bool
		{
			return count($tokens) === 4
					&& $tokens[0] === 'Mr'
					&& $tokens[1] === 'and'
					&& $tokens[2] === 'Mrs'
					&& $tokens[3] !== '';
		}

		/**
		 * @return array<int, Person>
		 */
		private function makeMrAndMrsSharedLastName(string $lastName): array
		{
			return [
					new Person(title: 'Mr', firstName: null, lastName: $lastName, initial: null),
					new Person(title: 'Mrs', firstName: null, lastName: $lastName, initial: null),
			];
		}

		private function detectJoiner(string $name): ?string
		{
			foreach (self::JOINERS as $joiner) {
				if (Str::contains($name, ' ' . $joiner . ' ')) {
					return $joiner;
				}
			}

			return null;
		}

		/**
		 * @return array{0:string,1:string}|null
		 */
		private function splitByJoiner(string $name, string $joiner): ?array
		{
			$needle = ' ' . $joiner . ' ';
			$pieces = explode($needle, $name, 2);

			if (count($pieces) !== 2) {
				return null;
			}

			$left = trim($pieces[0]);
			$right = trim($pieces[1]);

			if ($left === '' || $right === '') {
				return null;
			}

			return [$left, $right];
		}

		/**
		 * Supported single-person format: Title + (FirstName|Initial) + LastName
		 *
		 * @return array<int, Person>
		 */
		private function parseSingle(string $name): array
		{
			$tokens = preg_split('/\s+/', trim($name)) ?: [];

			if (count($tokens) !== 3) {
				return [];
			}

			if (! in_array($tokens[0], self::TITLES, true)) {
				return [];
			}

			$title = $tokens[0];
			$middle = $tokens[1];
			$lastName = $tokens[2];

			$firstName = $middle;
			$initial = null;

			if ($this->isInitialToken($middle)) {
				$firstName = null;
				$initial = $this->normalizeInitial($middle);
			}

			return [
					new Person(
							title: $title,
							firstName: $firstName,
							lastName: $lastName,
							initial: $initial,
					),
			];
		}

		/**
		 * Returns true for tokens like "M" or "F." (fixture-scoped).
		 */
		private function isInitialToken(string $token): bool
		{
			$token = trim($token);

			if ($token === '') {
				return false;
			}

			return preg_match('/^[A-Za-z]\.?$/', $token) === 1;
		}

		/**
		 * Normalize "f" / "F." into "F".
		 */
		private function normalizeInitial(string $token): string
		{
			return strtoupper(rtrim(trim($token), '.'));
		}
	}